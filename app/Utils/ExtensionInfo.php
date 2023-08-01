<?php

namespace App\Utils;

use Illuminate\Support\Arr;

class ExtensionInfo
{
    protected const AVAILABLE = [
        'mkv' => [
            'type' => 'video/x-matroska',
            'codecs' => [
                'video' => [
                    'H.264' => 'libx264',
                    'H.265' => 'libx265',
                    'MPEG-4' => 'mpeg4',
                ],
                'audio' => [
                    'AAC' => 'aac',
                    'MP3' => 'libmp3lame',
                ],
            ]
        ],
        'mp4' => [
            'type' => 'video/mp4',
            'codecs' => [
                'video' => [
                    'H.264' => 'libx264',
                    'H.265' => 'libx265',
                    'MPEG-4' => 'mpeg4',
                ],
                'audio' => [
                    'AAC' => 'aac',
                    'MP3' => 'libmp3lame',
                ],
            ]
        ],
        'avi' => [
            'type' => 'video/avi',
            'codecs' => [
                'video' => [
                    'Xvid' => 'libxvid',
                    'MPEG-4' => 'mpeg4',
                    'H.264' => 'libx264',
                ],
                'audio' => [
                    'MP3' => 'libmp3lame',
                    'MP2' => 'libtwolame',
                ],
            ]
        ],
    ];

    /**
     * Get the available extensions.
     *
     * @return array
     */
    public static function getExtensions(): array
    {
        return array_keys(self::AVAILABLE);
    }

    /**
     * Get the name extension name for a mime type.
     *
     * @param string $extension - The extension to lookup.
     * @return string|null the name, or null if the name is not found.
     */
    public static function getExtensionNameByType(string $type): ?string
    {
        foreach (self::AVAILABLE as $key => $info) {
            if ($info['type'] === $type) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Get the MIME type for a given extension.
     *
     * @param string $extension - The extension to lookup.
     * @return string|null the type, or null if the type is not found.
     */
    public static function getTypeByExtensionName(string $extension): ?string
    {
        return self::AVAILABLE[$extension]['type'] ?? null;
    }

    /**
     * Get the code by name.
     *
     * @param string $extension The extension.
     * @param string $type The type.
     * @param mixed $name The name.
     * @return string|null the code, or null if the extension is not found.
     */
    public static function getCodecByName(string $extension, string $type, mixed $name): ?string
    {
        return self::AVAILABLE[$extension][$type][$name] ?? null;
    }

    /**
     * Retrieves the list of codecs associated with a given extension name.
     *
     * @param string $extension The extension name.
     * @return array|null The list of codecs associated with the extension name, or null if the extension is not found.
     */
    public static function listCodecsByExtensionName(string $extension): ?array
    {
        $conversion = self::AVAILABLE[$extension] ?? null;
        if (!$conversion) {
            return null;
        }

        return self::AVAILABLE[$extension]['codecs'];
    }

    /**
     * Get the available extensions excluding the specified ones.
     *
     * @param string $remove - The extension to remove.
     * @return array
     */
    public static function getTypeAvailableByExtension(string $remove): array
    {
        $filtered = Arr::where(self::AVAILABLE, function ($value, $key) use ($remove) {
            return !in_array($value['type'], [$remove]);
        });
        return array_keys($filtered);
    }

    /**
     * Get the friendly codec name by extension and type.
     *
     * @param string $extension The file extension.
     * @param string $type The codec type.
     * @return string|null The friendly codec name, or null if not found.
     */
    public static function getFriendlyCodecNameByType(string $extension, string $type): ?string
    {
        $availableCodecs = self::AVAILABLE[$extension]['codecs'];

        foreach ($availableCodecs as $codec) {
            foreach ($codec as $key => $value) {
                if ($type == $value) {
                    return $key;
                }
            }
        }

        return null;
    }
}
