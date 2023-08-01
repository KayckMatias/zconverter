<?php

namespace App\Services\Downloader;

use App\Events\DownloadProgressEvent;
use App\Models\File;

class DownloadProgressService
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public static function make(File $file)
    {
        return new self($file);
    }

    /**
     * Set the job id.
     *
     * @param int $id The job id.
     * @return self
     */
    public function setJobId(int $id): self
    {
        $this->updateInDB([
            'job_id' => $id
        ]);

        return $this;
    }

    /**
     * Update the download progress.
     *
     * @param int $progress The progress of the download.
     * @return self
     */
    public function updateProgress(int $progress): self
    {
        $this->updateInDB([
            'progress' => $progress
        ]);

        return $this;
    }

    /**
     * Update the download status.
     *
     * @param string $status The new status.
     * @return self
     */
    public function updateStatus(string $status): self
    {
        $this->updateInDB([
            'status' => $status
        ]);

        return $this;
    }

    /**
     * Update the download message.
     *
     * @param string $message The new message.
     * @return self
     */
    public function updateMessage(string $message): self
    {
        $this->updateInDB([
            'message' => $message
        ]);

        return $this;
    }

    /**
     * Get the previous progress.
     *
     * @return int The previous progress.
     */
    public function getPreviousProgress(): int
    {
        return $this->file->downloadStatus->progress ?? 0;
    }

    /**
     * Update the download status in the database.
     *
     * @param Array $update The data to update the download status.
     * @return void
     */
    public function updateInDB(array $update): void
    {
        $this->file->downloadStatus()->update($update);
    }

    /**
     * Notify the user of the download progress.
     *
     * @return void
     */
    public function notifyUser(): void
    {
        event(new DownloadProgressEvent($this->file));
    }
}
