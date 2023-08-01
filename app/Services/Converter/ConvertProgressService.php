<?php

namespace App\Services\Converter;

use App\Events\ConvertProgressEvent;
use App\Models\ConvertStatus;

class ConvertProgressService
{
    private $convert_status;

    public function __construct($convert_status)
    {
        $this->convert_status = $convert_status;
    }

    public static function make(ConvertStatus $convert_status)
    {
        return new self($convert_status);
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
     * Update the convert progress.
     *
     * @param int $progress The progress of the convert.
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
     * Update the convert status.
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
     * Update the convert message.
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
        return $this->convert_status->progress ?? 0;
    }

    /**
     * Update the convert status in the database.
     *
     * @param Array $update The data to update the convert status.
     * @return void
     */
    public function updateInDB(array $update): void
    {
        $this->convert_status->update($update);
    }

    /**
     * Notify the user of the convert progress.
     *
     * @return void
     */
    public function notifyUser(): void
    {
        event(new ConvertProgressEvent($this->convert_status));
    }
}
