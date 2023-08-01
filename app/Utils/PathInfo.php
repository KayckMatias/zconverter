<?php

namespace App\Utils;

class PathInfo
{
    public const DOWNLOADING = 'tmp_download/';
    public const DOWNLOADED = 'files/';
    public const CONVERTED = 'converted/';

    /**
     * Resolve the converted path for a file.
     *
     * @param int $convert_id The ID of the conversion.
     * @param string $file_name The name of the file.
     * @param string $extension The file extension.
     * @return string The converted path.
     */
    public static function resolveConvertedPath(int $convert_id, string $file_name, string $extension): string
    {
        return self::CONVERTED .  $convert_id . '_' . $file_name . "." . $extension;
    }

    /**
     * Resolves the downloading path for a given file name.
     *
     * @param string $file_name The name of the file.
     *
     * @return string The downloading path for the file.
     */
    public static function resolveDownloadingPath(string $file_name): string
    {
        return self::DOWNLOADING . $file_name;
    }

    /**
     * Resolves the downloading temp path for a given file name.
     *
     * @param string $file_name The name of the file.
     *
     * @return string The downloading temp path for the file.
     */
    public static function resolveTempPath(string $file_name): string
    {
        return self::DOWNLOADING . $file_name . '.tmp';
    }

    /**
     * Resolves the downloaded path for a given file name.
     *
     * @param string $file_name The name of the file.
     *
     * @return string The downloaded path for the file.
     */
    public static function resolveDownloadedPath(string $file_name): string
    {
        return self::DOWNLOADED . $file_name;
    }
}
