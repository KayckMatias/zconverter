<?php

namespace App\Services\Converter;

use App\Jobs\ConvertJob;
use App\Models\ConvertStatus;
use App\Models\File;
use App\Services\Converter\ConvertProgressService;
use App\Utils\ResponseJson;
use Illuminate\Support\Facades\Bus;

class NewConvertFileService
{
    private $file;
    private $convert_status;
    private $extension;
    private $options;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public static function make(File $file)
    {
        return new self($file);
    }

    /**
     * Set the extension of the file.
     *
     * @param string $extension The extension of the file.
     * @return self
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Set the options for the class.
     *
     * @param array $options The options to set.
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Register the conversion status.
     *
     * @throws \Exception If the extension is not set.
     *
     * @return void
     */
    public function register(): void
    {
        if (!$this->extension) {
            throw new \Exception('Extension not found! setExtension() before register');
        }

        $this->convert_status = ConvertStatus::create(
            [
                'file_id' => $this->file->id,
                'to_extension' => $this->extension,
                'options' => json_encode($this->options)
            ]
        );
    }

    /**
     * Convert the file.
     *
     * @return \Illuminate\Http\JsonResponse The response indicating the conversion has been queued.
     */
    public function convert()
    {
        $job = Bus::dispatch(new ConvertJob($this->file, $this->convert_status, $this->extension, $this->options));
        ConvertProgressService::make($this->convert_status)->setJobId($job);

        return ResponseJson::default('Conversion on Queue!', 201);
    }
}
