<?php

namespace App\Services\File;

use App\Services\File\FilePathService;
use App\Utils\ExtensionInfo;
use FFMpeg\FFProbe;

class MetadataService
{
    private $file;
    protected function __construct($file)
    {
        $this->file = $file;
    }

    public static function set($file)
    {
        return new self($file);
    }

    /**
     * Returns a list of options to convert a file with a given extension.
     *
     * @param string $extension The extension of the file.
     * @return array An array containing the resolutions, video codecs, and audio codecs.
     */
    public function listOptionsToConvert(string $extension): array
    {
        $resolutions = $this->getSimilarResolutions($this->getFileResolution());

        $codecs = ExtensionInfo::listCodecsByExtensionName($extension);

        return [
            'resolutions' => $resolutions,
            'video' => $codecs['video'],
            'audio' => $codecs['audio'],
        ];
    }

    /**
     * Retrieves the file mime type.
     *
     * @return string
     */
    public function getFileMimeType(): string
    {
        $path = $this->getFilePath();
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $path);

        return $mimeType;
    }

    /**
     * Retrieves the available conversions for a given file based on its MIME type.
     *
     * @return array
     */
    public function getAvailableConversions(): array
    {
        $mimeType = $this->getFileMimeType();

        return ExtensionInfo::getTypeAvailableByExtension($mimeType);
    }

    /**
     * Get the resolution of the file.
     *
     * @return string|null The resolution of the file in the format "width x height", or null if an error occurs.
     * @throws \Exception If an error occurs while retrieving the resolution.
     */
    public function getFileResolution(): ?string
    {
        $path = $this->getFilePath();
        try {
            $ffprobe = FFProbe::create();
            $streamInfo = $ffprobe->streams($path)->videos()->first();

            $width = $streamInfo->get('width');
            $height = $streamInfo->get('height');

            $resolution = $width . 'x' . $height;
            return $resolution;
        } catch (\Exception $e) {
            throw new \Exception("Error retrieving file resolution: " . $e->getMessage());
        }
    }

    /**
     * @param string $originalResolution The original resolution in the format "widthxheight"
     * @return array The array of similar resolutions in the format "widthxheight"
     */
    public function getSimilarResolutions(string $originalResolution): array
    {
        [$originalWidth, $originalHeight] = explode('x', $originalResolution);

        $ratios = [3 / 4, 2 / 3, 1 / 2, 1 / 3];
        $similarResolutions = [];

        foreach ($ratios as $ratio) {
            $width = intval($originalWidth * $ratio);
            $height = intval($originalHeight * $ratio);

            if ($width >= 256 && $height >= 144) {
                $similarResolutions[] = $width . 'x' . $height;
            }
        }

        return $similarResolutions;
    }

    /**
     * Get the file path.
     *
     * @return string The downloaded real file path.
     */
    public function getFilePath(): string
    {
        $filePathService = FilePathService::make($this->file);
        return $filePathService->getDownloadedRealFilePath();
    }

    /**
     * Get the current codecs of the file.
     *
     * @return array|null An array containing the video and audio codecs.
     *                   Returns null if an exception occurs.
     *
     * @throws \Exception If an error occurs while retrieving the codecs.
     */
    public function getCurrentCodecs(): ?array
    {
        try {
            $ffprobe = FFProbe::create();
            $streams = $ffprobe->streams($this->getFilePath());

            $videoCodec = $streams->videos()->first()->get('codec_name');
            $audioCodec = $streams->audios()->first()->get('codec_name');

            return [
                'video' => $videoCodec,
                'audio' => $audioCodec,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
