<?php

namespace App\Services\File;

use App\Models\ConvertStatus;
use App\Models\File;
use App\Utils\ExtensionInfo;
use App\Utils\PathInfo;
use Illuminate\Support\Facades\Storage;

class FilePathService
{
    private File $file;

    private $storage;

    public function __construct(File $file)
    {
        $this->file = $file;
        $this->storage = Storage::disk('local');
    }

    public static function make(File $file)
    {
        return new self($file);
    }

    /**
     * Get the downloaded real file path.
     *
     * @return string The downloaded real file path.
     */
    public function getDownloadedRealFilePath(): string
    {
        $path = PathInfo::resolveDownloadedPath($this->file->path_name);
        return $this->storage->path($path);
    }

    /**
     * Get the converted real file path.
     *
     * @param int $conversionId The conversion ID.
     * @param string $extension The file extension.
     * @return string The converted real file path.
     */
    public function getConvertedRealFilePath(int $conversionId, string $extension): string
    {
        $path = PathInfo::resolveConvertedPath($conversionId, $this->file->path_name, $extension);
        return $this->storage->path($path);
    }

    /**
     * Deletes temporary files for a given file.
     *
     * @return self
     */
    public function deleteTempFiles(): self
    {
        $temp_path = PathInfo::resolveTempPath($this->file->path_name);
        $this->delete($temp_path);
        return $this;
    }

    /**
     * Delete the downloaded files.
     *
     * @return self
     */
    public function deleteDownloadedFiles(): self
    {
        $temp_path = PathInfo::resolveDownloadedPath($this->file->path_name);

        $this->delete($temp_path);
        return $this;
    }

    /**
     * Delete converted files.
     *
     * @param int|null $single The ID of the single file to delete.
     * @return $this
     */
    public function deleteConvertedFiles(?int $single = null): self
    {
        $this->file
            ->convertStatus()
            ->when($single, function ($q) use ($single) {
                $q->where('id', $single);
            })
            ->get()
            ->map(function ($item) {
                $path = PathInfo::resolveConvertedPath(
                    $item->id,
                    $this->file->path_name,
                    $item->to_extension
                );
                $this->delete($path);
            });

        return $this;
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path The path of the file to delete.
     * @return void
     */
    public function delete(string $path): void
    {
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
