<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * @param Request $request
     * @param string $fieldName Nama field file di form (contoh: 'foto')
     * @param string $directory Folder penyimpanan di disk 'public' (contoh: 'lampus')
     * @return array URL file yang disimpan.
     */
    public function handleMultipleUpload(Request $request, string $fieldName, string $directory): array
    {
        $paths = [];
        
        if ($request->hasFile($fieldName)) {
            $files = $request->file($fieldName);
            $fileArray = is_array($files) ? $files : [$files];
            $filesToProcess = array_slice($fileArray, 0, 4);

            foreach ($filesToProcess as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                    $path = $file->store($directory, 'public');
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }


    /**
     * @param Request $request
     * @param object $modelInstance Instance model Eloquent (contoh: $lampu)
     * @param string $fieldName Nama field file di form (contoh: 'foto')
     * @param string $urlFieldName Nama kolom JSON array URL di database (contoh: 'foto_urls')
     * @param string $directory Folder penyimpanan (contoh: 'cctvs')
     * @return array Array URL file yang baru/lama.
     */
    public function updateMultipleUpload(
        Request $request, 
        object $modelInstance, 
        string $fieldName, 
        string $urlFieldName,
        string $directory
    ): array {
        $existingUrls = $modelInstance->{$urlFieldName} ?? [];
        
        if (!is_array($existingUrls)) {
            $existingUrls = json_decode($existingUrls, true) ?? [];
        }

        if ($request->hasFile($fieldName)) {
            $this->deleteMultipleFiles($existingUrls);
            return $this->handleMultipleUpload($request, $fieldName, $directory);
        }
        
        return $existingUrls;
    }
    
    /**
     * @param array $fileUrls Array URL file yang akan dihapus.
     */
    public function deleteMultipleFiles(array $fileUrls): void
    {
        if (empty($fileUrls)) {
            return;
        }

        foreach ($fileUrls as $url) {
            if ($url) {
                $path = str_replace(Storage::url(''), '', $url);
                Storage::disk('public')->delete($path);
            }
        }
    }
}