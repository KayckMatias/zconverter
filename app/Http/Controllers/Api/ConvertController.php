<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ConvertJob;
use App\Models\ConvertStatus;
use App\Models\File;
use App\Services\Converter\ConvertOptionsService;
use App\Services\Converter\ConvertProgressService;
use App\Services\Converter\NewConvertFileService;
use App\Services\File\FilePathService;
use App\Services\File\MetadataService;
use App\Utils\ResponseJson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Metadata\Metadata;

class ConvertController extends Controller
{
    public function list()
    {
        $data = ConvertStatus::whereHas('file', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->orderByDesc('created_at')->get()->map(function ($item) {
            $decodecOptions = json_decode($item->options, true);
            $friendlyOptions = ConvertOptionsService::make($item->file, $decodecOptions, $item->to_extension)->getFriendlyOptions();
            return [
                'id' => $item->id,
                'file_id' => $item->file->id,
                'original_name' => $item->file->original_name,
                'created_at' => $item->created_at,
                'status' => $item->status,
                'progress' => $item->progress,
                'last_download_update' => $item->updated_at,
                'to_extension' => $item->to_extension,
                'options' => $friendlyOptions
            ];
        });

        return response()->json(['files' => $data], 200);
    }

    public function getOptions($id, $extension)
    {
        $file = File::findOrFail($id);

        $can = $this->checkIfCan($file, $extension, 'options');

        if ($can !== true) {
            return $can;
        }

        $options = MetadataService::set($file)->listOptionsToConvert($extension);

        return response()->json(['options' => $options], 200);
    }

    public function new($id, $extension)
    {
        $options = request()->get('options') ?? [];
        $file = File::findOrFail($id);

        $can = $this->checkIfCan($file, $extension, 'new', $options);

        if ($can !== true) {
            return $can;
        }

        $convert = NewConvertFileService::make($file);
        $convert->setExtension($extension)
            ->setOptions($options)
            ->register();

        return $convert->convert();
    }

    public function delete($id)
    {
        $convert = ConvertStatus::findOrFail($id);

        if ($convert->file->user_id !== Auth::id()) {
            return ResponseJson::forbidden();
        }

        FilePathService::make($convert->file)
            ->deleteConvertedFiles($id);

        $convert->delete();

        return response()->json(['message' => 'Deleted!']);
    }

    public function retry($id)
    {
        $convert = ConvertStatus::findOrFail($id);

        if ($convert->file->user_id !== Auth::id()) {
            return ResponseJson::forbidden();
        }

        if($convert->status != 'FAILED') {
            return ResponseJson::unprocessable('This file not failed, you can\'t retry download, please, remove & re-download!');
        }

        $options = json_decode($convert->options, true);

        $job_id = Bus::dispatch(new ConvertJob($convert->file, $convert, $convert->to_extension, $options));

        ConvertProgressService::make($convert)->setJobId($job_id);

        return response()->json(['message' => 'Retrying convert...']);
    }

    public function checkIfCan(File $file, $extension, $type, $options = null)
    {
        if ($file->user_id !== Auth::id()) {
            return ResponseJson::forbidden();
        }

        $downloadStatus = $file->downloadStatus;

        if ($downloadStatus?->status != "COMPLETED") {
            return ResponseJson::unprocessable('This file is not ready to convert, make sure it download is completed!');
        }

        $metadata = MetadataService::set($file);
        $availableConversions = $metadata->getAvailableConversions();

        if (!in_array($extension, $availableConversions)) {
            return ResponseJson::unprocessable('This extension is not available to convert!');
        }

        if ($type == 'new') {
            $conversionExists = $file->whereHas('convertStatus', function ($q) {
                $q->where('status', '!=', 'COMPLETED');
            })->exists();

            if ($conversionExists) {
                return ResponseJson::unprocessable('You already have a conversion running for this file, wait convert or remove!');
            }
        }

        if ($options) {
            $checkEquals = $file->whereHas('convertStatus', function ($q) use ($extension, $options) {
                $q->where('to_extension', $extension);
                $q->where('options->resolution', $options['resolution']);
                $q->where('options->codec_audio', $options['codec_audio']);
                $q->where('options->codec_video', $options['codec_video']);
            })->exists();

            if ($checkEquals) {
                return ResponseJson::unprocessable('You already have a conversion identical for this file, download or remove!');
            }
        }

        return true;
    }
}
