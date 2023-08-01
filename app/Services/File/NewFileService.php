<?php

namespace App\Services\File;

use App\Jobs\DownloadJob;
use App\Models\DownloadStatus;
use App\Models\File;
use App\Services\Downloader\DownloadProgressService;
use Illuminate\Support\Facades\Bus;

class NewFileService
{
    private $url;
    private $original_name;
    private $file_id;

    public function __construct($url)
    {
        $this->url = $url;
        $this->original_name = basename($url);
    }

    public static function new($url)
    {
        return new self($url);
    }

    /**
     * Checks if the URL represents a video based on its file extension.
     *
     * @return bool Returns true if the URL represents a video, false otherwise.
     */
    public function isVideo(): bool
    {
        $extension = pathinfo($this->url, PATHINFO_EXTENSION);
        $videoExtensions = ['mp4', 'avi', 'mkv', 'mov', 'wmv'];

        return in_array($extension, $videoExtensions);
    }

    /**
     * Check the status of a URL.
     *
     * @return bool True if the URL returns a 200 status code, false otherwise.
     */
    public function check(): bool
    {
        $headers = @get_headers($this->url);

        if ($headers && strpos($headers[0], '200') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Register a file.
     *
     * @return self
     * @throws \Exception If there is an error registering the file.
     */
    public function register(): self
    {
        try {
            $file = File::create([
                'original_name' => $this->original_name,
                'path_name' => $this->getUniqueName(),
                'original_url' => $this->url,
                'user_id' => auth()->user()->id
            ]);

            $this->file_id = $file->id;
        } catch (\Exception $e) {
            throw new \Exception('Error on register file: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Download the file.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function download(): \Illuminate\Http\JsonResponse
    {
        $this->validateFileId();

        $this->createDownloadStatus();

        $file = File::findOrFail($this->file_id);

        $job = Bus::dispatch(new DownloadJob($file));

        DownloadProgressService::make($file)->setJobId($job);

        return response()->json(['message' => 'Download on Queue!'], 201);
    }

    /**
     * Validates the file ID.
     *
     * @throws \Exception If the file ID is not set.
     */
    private function validateFileId(): void
    {
        if (!$this->file_id) {
            throw new \Exception('File ID is not set.');
        }
    }

    /**
     * Creates a download status for the given file ID.
     *
     * @return void
     */
    private function createDownloadStatus(): void
    {
        DownloadStatus::firstOrCreate(['file_id' => $this->file_id]);
    }

    /**
     * Generates a unique name by appending a unique identifier to the original name.
     *
     * @return string The unique name.
     */
    private function getUniqueName(): string
    {
        return uniqid("", true) . '_' . $this->original_name;
    }
}
