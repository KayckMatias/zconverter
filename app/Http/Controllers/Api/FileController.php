<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConvertStatus;
use App\Models\File;
use App\Services\File\FilePathService;
use App\Services\File\SignedUrlService;
use App\Services\File\MetadataService;
use App\Utils\ResponseJson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class FileController extends Controller
{
    public function availableConvert($id)
    {
        $file = File::findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            return ResponseJson::forbidden();
        }

        $downloadStatus = $file->downloadStatus->status;
        $metadata = MetadataService::set($file);
        $availableConversions = ($downloadStatus === 'COMPLETED') ? $metadata->getAvailableConversions() : [];

        return [
            'id' => $file->id,
            'available' => $availableConversions,
        ];
    }

    public function generateDownloadLink($id, $convert_id)
    {
        $file = File::findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            return ResponseJson::forbidden();
        }

        $signedUrl = SignedUrlService::make($id, $convert_id)->generate();

        return response()->json([
            'url' => $signedUrl
        ]);
    }

    public function downloadConverted($id, $convert_id)
    {
        $convert = ConvertStatus::findOrFail($convert_id);

        if (!URL::hasValidSignature(request(), 'url')) {
            ResponseJson::forbidden('Invalid URL or not exists.');
        }

        $download_path = FilePathService::make($convert->file)
            ->getConvertedRealFilePath($convert->id, $convert->to_extension);

        if (!file_exists($download_path)) {
            return ResponseJson::notFound();
        }

        $download_name = config('app.name') . '_' . basename($download_path);

        return response()->download($download_path, $download_name);
    }
}
