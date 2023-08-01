<?php

namespace App\Jobs;

use App\Models\File;
use App\Services\Downloader\DownloadProgressService;
use App\Utils\PathInfo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;

class DownloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $timeout = 0;

    public $tries = 1;

    public $queue = 'downloader';

    protected $file;

    private $temp_path, $final_path;

    /**
     * Create a new job instance.
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->initializePaths();

        $fileTrack = $this->file;
        $status = DownloadProgressService::make($fileTrack);

        try {
            $status->updateStatus('DOWNLOADING')->notifyUser();

            Http::withOptions([
                'timeout' => null,
                'sink' => Storage::path($this->temp_path),
                'progress' => function ($totalSize, $downloadedSize) use ($status) {
                    $this->verifyIfCancel();

                    if ($totalSize > 0) {
                        RateLimiter::attempt(
                            'updateDownloaderProgress:' . $this->file->id,
                            1,
                            function () use ($downloadedSize, $totalSize, $status) {
                                $downloadedProgress = round(($downloadedSize / $totalSize) * 100);
                                $previousProgress = $status->getPreviousProgress();
                                if ($downloadedProgress > $previousProgress) {
                                    $status->updateProgress($downloadedProgress)->notifyUser();
                                }
                            },
                            5
                        );
                    }
                }
            ])->get($this->file->original_url);
        } catch (\Exception $e) {
            $this->fail($e);
        }

        Storage::move($this->temp_path, $this->final_path);

        $status->updateStatus('COMPLETED')
            ->updateProgress(100)
            ->notifyUser();
    }

    /**
     * @param string $pathName
     * @return void
     */
    private function initializePaths()
    {
        $this->temp_path = PathInfo::resolveTempPath($this->file->path_name);
        $this->final_path = PathInfo::resolveDownloadedPath($this->file->path_name);
    }


    /**
     * Verify if cancellation is required.
     */
    private function verifyIfCancel(): void
    {
        if (!File::find($this->file->id)?->downloadstatus?->job_id) {
            $this->fail(new \Exception('Download Status not found, cancel job'));
        }
    }

    public function failed(\Throwable $e)
    {
        if (isset($this->temp_path) && Storage::exists($this->temp_path)) Storage::delete($this->temp_path);

        $status = DownloadProgressService::make($this->file);
        $status->updateStatus('FAILED')
            ->updateMessage('Job Failed! Message: ' . $e)
            ->notifyUser();
    }
}
