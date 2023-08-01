<?php

namespace App\Jobs;

use App\Models\ConvertStatus;
use App\Models\File;
use App\Services\Converter\ConvertOptionsService;
use App\Services\Converter\ConvertProgressService;
use App\Utils\PathInfo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\RateLimiter;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $timeout = 0;

    public $tries = 1;

    public $queue = 'converter';

    protected $file, $convert_status, $extension, $options;

    private $file_path, $final_path;

    /**
     * Create a new job instance.
     */
    public function __construct(File $file, ConvertStatus $convert_status, string $extension, ?array $options)
    {
        $this->file = $file;
        $this->convert_status = $convert_status;
        $this->extension = $extension;
        $this->options = $options ?? [];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->initializePaths();

        $status = ConvertProgressService::make($this->convert_status);

        $options = ConvertOptionsService::make($this->file, $this->options, $this->extension);
        [$w, $h] = $options->getResolution();
        $format = $options->getFormat();

        $status->updateStatus('CONVERTING')->notifyUser();

        try {
            FFMpeg::open($this->file_path)
                ->export()
                ->inFormat($format)
                ->resize($w, $h)
                ->onProgress(function ($percentage) use ($status) {
                    RateLimiter::attempt(
                        'updateConverterProgress:' . $this->file->id,
                        1,
                        function () use ($percentage, $status) {
                            $previousProgress = $status->getPreviousProgress();
                            if ($percentage > $previousProgress) {
                                $status->updateProgress($percentage)->notifyUser();
                            }
                        },
                        5
                    );
                })
                ->save($this->final_path);
        } catch (EncodingException $exception) {
            $command = $exception->getCommand();
            $errorLog = $exception->getErrorOutput();
            $this->fail($command . ' | ' . $errorLog);
        } catch (\Exception $e) {
            $this->fail($e);
        }

        $status->updateStatus('COMPLETED')
            ->updateProgress(100)
            ->notifyUser();
    }

    /**
     * Initialize the paths for the file.
     *
     * @return void
     */
    private function initializePaths(): void
    {
        $this->file_path = PathInfo::resolveDownloadedPath($this->file->path_name);
        $this->final_path = PathInfo::resolveConvertedPath(
            $this->convert_status->id,
            $this->file->path_name,
            $this->extension
        );
    }

    public function failed(\Throwable $e)
    {
        $status = ConvertProgressService::make($this->convert_status);
        $status->updateStatus('FAILED')
            ->updateMessage('Job Failed! Message: ' . $e)
            ->notifyUser();
    }
}
