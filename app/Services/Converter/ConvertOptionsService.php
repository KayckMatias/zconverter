<?php

namespace App\Services\Converter;

use App\Custom\FFMpegFormat;
use App\Models\File;
use App\Services\File\MetadataService;
use App\Utils\ExtensionInfo;

class ConvertOptionsService
{
    private $file;
    private $options;
    private $extension;

    public function __construct($file, $options, $extension)
    {
        $this->file = $file;
        $this->options = $options;
        $this->extension = $extension;
    }

    public static function make(File $file, array $options, string $extension)
    {
        return new self($file, $options, $extension);
    }

    /**
     * Returns the FFMpegFormat object with the validated audio and video codecs.
     *
     * @return FFMpegFormat The FFMpegFormat object.
     */
    public function getFormat(): FFMpegFormat
    {
        $video = $this->validateCodec($this->extension, 'video', $this->options['codec_video']) ?? 'libx264';
        $audio = $this->validateCodec($this->extension, 'audio', $this->options['codec_audio']) ?? 'aac';

        return new FFMpegFormat($audio, $video);
    }

    /**
     * Get the resolution of the file.
     *
     * @return array The resolution as an array of two integers.
     */
    public function getResolution(): array
    {
        $resolution = (!$this->options['resolution']) ? MetadataService::set($this->file)->getFileResolution() : $this->options['resolution'];

        return explode('x', $resolution);
    }

    /**
     * Returns an array of friendly options.
     *
     * @return array The array of friendly options.
     */
    public function getFriendlyOptions(): array
    {
        return array_map(function ($default) {
            $friendly = $default ? ExtensionInfo::getFriendlyCodecNameByType($this->extension, $default) : null;
            return $friendly ?? $default;
        }, $this->options);
    }

    /**
     * Validates the codec for a given extension and type.
     *
     * @param string $extension The extension to validate.
     * @param string|null $codec The codec to validate.
     * @param string $type The type of codec.
     * @return string|null The validated codec, or null if invalid.
     */
    private function validateCodec(string $extension, string $type, ?string $codec = 'default'): ?string
    {
        if ($codec === 'default') {
            return null;
        }

        $list_codecs = ExtensionInfo::listCodecsByExtensionName($extension)[$type];

        return in_array($codec, $list_codecs) ? $codec : null;
    }
}
