<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller; 
use App\Models\Setting; 
use Illuminate\Http\Request; 

class FolderManagerController extends Controller
{
    public function index($path = null)
    {
        $businessId = my_business();
        $basePath = 'uploads/folders/' . $businessId;

        // Sanitize path untuk security
        $requestedPath = $path ? trim($path, '/') : '';
        $fullPath = $basePath . ($requestedPath ? '/' . $requestedPath : '');

        // Security: pastikan path tidak keluar dari base directory
        $realBasePath = realpath(public_path($basePath));
        $realFullPath = realpath(public_path($fullPath));

        // if (!$realFullPath || strpos($realFullPath, $realBasePath) !== 0) {
        //     abort(403, 'Invalid path');
        // }

        // Buat base directory jika belum ada
        if (!file_exists(public_path($basePath))) {
            mkdir(public_path($basePath), 0755, true);
        }

        // Scan directory
        $folders = [];
        $media = [];

        if (file_exists(public_path($fullPath))) {
            $items = scandir(public_path($fullPath));

            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;

                $itemPath = $fullPath . '/' . $item;
                $itemFullPath = public_path($itemPath);

                if (is_dir($itemFullPath)) {
                    // Ini adalah folder
                    $folders[] = [
                        'name' => $item,
                        'slug' => $item,
                        'path' => $requestedPath ? $requestedPath . '/' . $item : $item,
                        'created_at' => date('Y-m-d H:i:s', filectime($itemFullPath)),
                        'size' => $this->getFolderSize($itemFullPath),
                        'item_count' => count(scandir($itemFullPath)) - 2 // minus . dan ..
                    ];
                } else {
                    // Ini adalah file
                    $extension = pathinfo($item, PATHINFO_EXTENSION);
                    $media[] = [
                        'name' => $item,
                        'path' => $itemPath,
                        'format' => $extension,
                        'mime_type' => mime_content_type($itemFullPath),
                        'size' => filesize($itemFullPath),
                        'size_formatted' => $this->formatBytes(filesize($itemFullPath)),
                        'created_at' => date('Y-m-d H:i:s', filectime($itemFullPath)),
                        'modified_at' => date('Y-m-d H:i:s', filemtime($itemFullPath)),
                        'url' => asset($itemPath)
                    ];
                }
            }
        }

        // Sort folders and media by name
        usort($folders, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        usort($media, fn($a, $b) => strcasecmp($a['name'], $b['name']));

        // Build breadcrumb
        $directory = $requestedPath ? explode('/', $requestedPath) : [];

        // Get parent path
        $parentPath = null;
        if (count($directory) > 0) {
            $parentArray = array_slice($directory, 0, -1);
            $parentPath = count($parentArray) > 0 ? implode('/', $parentArray) : '';
        }

        return view('media.folder', [
            'current_folder' => $requestedPath ? end($directory) : null,
            'sub_folders' => $folders,
            'media' => $media,
            'directory' => $directory,
            'path' => $requestedPath,
            'parent_path' => $parentPath,
            'page' => __('sidebar.media_manager'),
            'breadcumb' => true,
        ]);
    }

    public function insertFolder(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|regex:/^[a-zA-Z0-9_\-\s]+$/', // Hanya karakter aman
            'current_path' => 'nullable|string'
        ]);

        $businessId = my_business();
        $folderName = trim($request->name);
        $currentPath = $request->current_path ? trim($request->current_path, '/') : '';

        // Create slug
        $slug = $this->createSlug($folderName);

        // Build full path
        $basePath = 'uploads/folders/' . $businessId;
        $fullPath = $basePath . ($currentPath ? '/' . $currentPath : '') . '/' . $slug;
        $fullPathAbsolute = public_path($fullPath);

        // Check if folder already exists
        if (file_exists($fullPathAbsolute)) {
            return redirect()->back()->with(['gagal' => 'Folder sudah ada!']);
        }

        // Create directory
        if (!mkdir($fullPathAbsolute, 0755, true)) {
            return redirect()->back()->with(['gagal' => 'Gagal membuat folder!']);
        }

        return redirect()->back()->with(['flash' => __('general.success_add_data')]);
    }

    public function insertMedia(Request $request)
    {
        $request->validate([
            'current_path' => 'nullable|string',
            'file' => 'required|mimes:png,jpg,jpeg,ogg,mp3,mp4,docx,xlsx,webp,pdf,csv,txt,ppt|max:3072',
        ]);

        $businessId = my_business();
        $setting = Setting::find($businessId);

        // Check storage availability
        if ($request->hasFile('file')) {
            $fileSize = $request->file('file')->getSize();
            $storageCheck = $this->checkStorage($setting, $fileSize);

            if (!$storageCheck['has_package']) {
                return redirect()->back()->with([
                    'gagal' => 'Anda belum memiliki paket storage. Silakan subscribe paket terlebih dahulu.'
                ])->with([
                    'redirect_billing' => true
                ]);
            }

            if (!$storageCheck['available']) {
                return redirect()->back()->with([
                    'gagal' => 'Storage Anda penuh! Tersisa: ' . number_format($storageCheck['remaining_storage'], 2) . ' MB. Ukuran file: ' . number_format($storageCheck['file_size'], 2) . ' MB. Silakan upgrade paket Anda.'
                ])->with([
                    'redirect_billing' => true
                ]);
            }
        }

        $currentPath = $request->current_path ? trim($request->current_path, '/') : '';
        $basePath = 'uploads/folders/' . $businessId;
        $uploadPath = $basePath . ($currentPath ? '/' . $currentPath : '');
        $uploadPathAbsolute = public_path($uploadPath);

        // Ensure directory exists
        if (!file_exists($uploadPathAbsolute)) {
            mkdir($uploadPathAbsolute, 0755, true);
        }

        // Generate unique filename
        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = $this->createSlug($originalName) . '_' . time() . '.' . $extension;

        // Move file
        $file->move($uploadPathAbsolute, $fileName);

        return redirect()->back()->with(['flash' => __('general.success_add_data')]);
    }

    public function deleteMedia(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        $businessId = my_business();
        $filePath = trim($request->path, '/');

        // Security check - pastikan path dimulai dengan business folder
        $basePath = 'uploads/folders/' . $businessId;
        if (strpos($filePath, $basePath) !== 0) {
            return redirect()->back()->with(['gagal' => 'Unauthorized access!']);
        }

        // Additional security - decode path dan check real path
        $filePathAbsolute = public_path($filePath);
        $basePathAbsolute = public_path($basePath);

        $realFilePath = realpath($filePathAbsolute);
        $realBasePath = realpath($basePathAbsolute);

        // Pastikan file ada dalam base directory
        if (!$realFilePath || strpos($realFilePath, $realBasePath) !== 0) {
            return redirect()->back()->with(['gagal' => 'Invalid file path!']);
        }

        if (file_exists($filePathAbsolute) && is_file($filePathAbsolute)) {
            unlink($filePathAbsolute);
            return redirect()->back()->with(['flash' => __('general.success_deleted')]);
        }

        return redirect()->back()->with(['gagal' => 'File tidak ditemukan']);
    }

    public function deleteFolder(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        $businessId = my_business();
        $folderPath = trim($request->path, '/');

        // Security check
        $basePath = 'uploads/folders/' . $businessId;
        $fullPath = $basePath . '/' . $folderPath;

        if (strpos($fullPath, $basePath) !== 0) {
            return redirect()->back()->with(['gagal' => 'Unauthorized access!']);
        }

        $folderPathAbsolute = public_path($fullPath);
        $basePathAbsolute = public_path($basePath);

        $realFolderPath = realpath($folderPathAbsolute);
        $realBasePath = realpath($basePathAbsolute);

        // Prevent deleting base directory
        if ($realFolderPath === $realBasePath) {
            return redirect()->back()->with(['gagal' => 'Cannot delete base folder!']);
        }

        // Pastikan folder ada dalam base directory
        if (!$realFolderPath || strpos($realFolderPath, $realBasePath) !== 0) {
            return redirect()->back()->with(['gagal' => 'Invalid folder path!']);
        }

        if (file_exists($folderPathAbsolute) && is_dir($folderPathAbsolute)) {
            // Delete folder recursively
            $this->deleteDirectory($folderPathAbsolute);

            // Get parent path for redirect
            $pathParts = explode('/', $folderPath);
            array_pop($pathParts); // Remove last part (current folder)
            $parentPath = count($pathParts) > 0 ? implode('/', $pathParts) : '';

            $redirectRoute = $parentPath
                ? route('folders', ['path' => $parentPath])
                : route('folders');

            return redirect($redirectRoute)->with(['flash' => __('general.success_deleted')]);
        }

        return redirect()->back()->with(['gagal' => 'Folder tidak ditemukan']);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }


    private function getFolderSize($path)
    {
        $size = 0;

        if (!is_dir($path)) {
            return filesize($path);
        }

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }
 

    private function createSlug($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }

    public function checkStorage(Setting $setting, $fileSize = 0)
    {
        if ($setting->merchant) {
            $totalSize = 0;
            $path = public_path("uploads/folders/{$setting->id}");

            if (file_exists($path)) {
                $totalSize = $this->getFolderSize($path);
            }

            // Convert to MB
            $usedStorageMB = round($totalSize / 1024 / 1024, 2);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            // Get total storage
            $storageFromSubscribe = $setting->package_active ? (int)$setting->package_active->storage : 0;
            $storageFromAddons = $setting->package_active_storage ? (int)$setting->package_active_storage->storage : 0;
            $totalStorage = $storageFromSubscribe + $storageFromAddons;

            // Check if storage is available
            $remainingStorage = $totalStorage - $usedStorageMB;

            return [
                'available' => $totalStorage > 0 && ($usedStorageMB + $fileSizeMB) <= $totalStorage,
                'total_storage' => $totalStorage,
                'used_storage' => $usedStorageMB,
                'remaining_storage' => $remainingStorage,
                'file_size' => $fileSizeMB,
                'has_package' => $totalStorage > 0
            ];
        } else {
            return [
                'available' => true,
                'total_storage' => 9999999,
                'used_storage' => 0,
                'remaining_storage' => 9999,
                'file_size' => 9999,
                'has_package' => 9999
            ];
        }
    }
}