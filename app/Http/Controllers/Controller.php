<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Ramsey\Uuid\Uuid;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function uploadImage(Request $request, $name, $path, $detail = false)
    {
        if ($detail) {
            if ($request->hasFile($name)) {
                $file = $request->file($name);
                $extension = strtolower($file->getClientOriginalExtension());
                $uuidFileName = Uuid::uuid4()->toString() . '.' . $extension;

                // Simpan file dengan nama UUID
                $filePath = $file->storeAs('uploads/' . $path, $uuidFileName);
                $fileType = $file->getClientMimeType();

                // Mendapatkan tipe file utama (text, image, application, dll)
                $mainType = explode('/', $fileType)[0];

                // Mapping tipe khusus untuk beberapa ekstensi umum
                $type = $mainType;
                if ($mainType === 'application') {
                    switch ($extension) {
                        case 'pdf':
                            $type = 'pdf';
                            break;
                        case 'xls':
                        case 'xlsx':
                            $type = 'xls';
                            break;
                        case 'doc':
                        case 'docx':
                            $type = 'doc';
                            break;
                        case 'ppt':
                        case 'pptx':
                            $type = 'ppt';
                            break;
                        default:
                            $type = 'application';
                            break;
                    }
                } elseif ($mainType === 'image') {
                    $type = 'image';
                } elseif ($mainType === 'text') {
                    $type = 'text';
                }

                // Nama asli tetap disimpan jika perlu
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                return [
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'type' => $type,
                    'file_name' => $originalName, // nama asli
                    'file_size' => $fileSize,
                    'file_name_stored' => $uuidFileName // nama file yang disimpan
                ];
            }
        } else {
            if ($request->hasFile($name)) {
                $file = $request->file($name);
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension(); 
                $filename = Uuid::uuid4()->toString() . '.' . $extension;

                return $file->storeAs('uploads/' . $path, $filename);;
            }
        }
        return null;
    }

    public function unlinkFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
