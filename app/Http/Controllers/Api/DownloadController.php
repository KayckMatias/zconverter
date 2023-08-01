<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadJob;
use App\Models\File;
use App\Services\Downloader\DownloadProgressService;
use App\Services\File\FilePathService;
use App\Services\File\NewFileService;
use App\Utils\ResponseJson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;

class DownloadController extends Controller
{
    public function list()
    {
        $data = File::authUser()->with('downloadstatus')->orderByDesc('created_at')
            ->get()->map(function ($item) {
                return [
                    "id" => $item->id,
                    "original_name" => $item->original_name,
                    "original_url" => $item->original_url,
                    "created_at" => $item->created_at,
                    "status" => $item->downloadstatus?->status ?? 'No info',
                    "progress" => $item->downloadstatus->progress ?? 'No info',
                    "last_download_update" => $item->downloadstatus->updated_at ?? 'No info',
                ];
            });

        return $data;
    }

    public function new()
    {
        $url = request()->get('url');

        $check = File::AuthUser()->where('original_url', $url)
            ->whereHas('downloadstatus')
            ->exists();

        if ($check) {
            return ResponseJson::unprocessable('This URL is already exists! Convert, try download again or remove!');
        }

        $download = NewFileService::new($url);

        if (!$download->isVideo()) {
            return ResponseJson::unprocessable('This URL not is a video!');
        }

        if (!$download->check()) {
            return ResponseJson::unprocessable('This URL not is acessible!');
        }

        return $download->register()->download();
    }

    public function retry($id)
    {
        $file = File::findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            return ResponseJson::forbidden();
        }

        if($file->downloadStatus?->status != 'FAILED') {
            return ResponseJson::unprocessable('This file not failed, you can\'t retry download, please, remove & re-download!');
        }

        $job_id = Bus::dispatch(new DownloadJob($file));

        DownloadProgressService::make($file)->setJobId($job_id);

        return response()->json(['message' => 'Retrying download...']);
    }

    public function delete($id)
    {
        $file = File::findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            return ResponseJson::forbidden();
        }

        FilePathService::make($file)
            ->deleteTempFiles()
            ->deleteDownloadedFiles()
            ->deleteConvertedFiles();

        $file->delete();

        return response()->json(['message' => 'Deleted!']);
    }
}
