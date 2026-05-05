<?php

namespace App\Services\Platform\Telegram;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramProcessorService
{
    private $data;
    private $device;
    private $typeMimeData;
    private $messageType = 'unknown';
    private $message = null;
    private $fileData = [];

    public function __construct(array $data, $device, array $typeMimeData)
    {
        $this->data = $data;
        $this->device = $device;
        $this->typeMimeData = $typeMimeData;
        $this->parseMessage();
    }

    public function isValidMessage(): bool
    {
        return isset($this->data['message']['chat']['id']) &&
            isset($this->data['message']['message_id']);
    }

    public function getFromNumber(): string
    {
        return $this->data['message']['chat']['id'] ?? '';
    }

    public function getMessageId(): string
    {
        return $this->data['message']['message_id'] ?? '';
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function hasFile(): bool
    {
        return !empty($this->fileData['file_id']);
    }

    public function getFilePath(): ?string
    {
        return $this->fileData['path'] ?? null;
    }

    public function getFileType(): ?string
    {
        return $this->fileData['type'] ?? null;
    }

    public function getFileSize(): ?int
    {
        return $this->fileData['size'] ?? null;
    }

    private function parseMessage(): void
    {
        $message = $this->data['message'] ?? [];

        if (!empty($message['text'])) {
            $this->messageType = 'text';
            $this->message = $message['text'];
        } elseif (!empty($message['photo'])) {
            $this->parsePhotoMessage($message);
        } elseif (!empty($message['video'])) {
            $this->parseVideoMessage($message);
        } elseif (!empty($message['document'])) {
            $this->parseDocumentMessage($message);
        } elseif (!empty($message['audio'])) {
            $this->parseAudioMessage($message);
        } elseif (!empty($message['voice'])) {
            $this->parseVoiceMessage($message);
        }
    }

    private function parsePhotoMessage(array $message): void
    {
        $this->messageType = 'image';
        $photoData = end($message['photo']);
        $this->message = $message['caption'] ?? '';
        $this->fileData = [
            'file_id' => $photoData['file_id'] ?? null,
            'file_unique_id' => $photoData['file_unique_id'] ?? null,
            'size' => $photoData['file_size'] ?? null,
        ];
    }

    private function parseVideoMessage(array $message): void
    {
        $this->messageType = 'video';
        $this->message = $message['caption'] ?? '';
        $videoData = $message['video'];
        $this->fileData = [
            'file_id' => $videoData['file_id'] ?? null,
            'file_unique_id' => $videoData['file_unique_id'] ?? null,
            'duration' => $videoData['duration'] ?? null,
            'size' => $videoData['file_size'] ?? null,
        ];
    }

    private function parseDocumentMessage(array $message): void
    {
        $this->messageType = 'document';
        $this->message = $message['caption'] ?? '';
        $docData = $message['document'];
        $this->fileData = [
            'file_id' => $docData['file_id'] ?? null,
            'file_unique_id' => $docData['file_unique_id'] ?? null,
            'file_name' => $docData['file_name'] ?? null,
            'mime_type' => $docData['mime_type'] ?? null,
            'size' => $docData['file_size'] ?? null,
        ];
    }

    private function parseAudioMessage(array $message): void
    {
        $this->messageType = 'audio';
        $this->message = $message['caption'] ?? '';
        $audioData = $message['audio'];
        $this->fileData = [
            'file_id' => $audioData['file_id'] ?? null,
            'file_unique_id' => $audioData['file_unique_id'] ?? null,
            'duration' => $audioData['duration'] ?? null,
            'mime_type' => $audioData['mime_type'] ?? null,
            'size' => $audioData['file_size'] ?? null,
        ];
    }

    private function parseVoiceMessage(array $message): void
    {
        $this->messageType = 'audio';
        $voiceData = $message['voice'];
        $this->fileData = [
            'file_id' => $voiceData['file_id'] ?? null,
            'file_unique_id' => $voiceData['file_unique_id'] ?? null,
            'duration' => $voiceData['duration'] ?? null,
            'mime_type' => $voiceData['mime_type'] ?? null,
            'size' => $voiceData['file_size'] ?? null,
        ];
    }

    public function downloadAndProcessFile(): void
    {
        if (!$this->hasFile()) {
            return;
        }

        try {
            $fileId = $this->fileData['file_id'];
            $token = $this->device->token;

            // Ambil info file dari Telegram
            $getFileUrl = "https://api.telegram.org/bot{$token}/getFile?file_id={$fileId}";
            $fileInfo = json_decode(file_get_contents($getFileUrl), true);

            if (!isset($fileInfo['ok']) || !$fileInfo['ok']) {
                throw new \Exception('Failed to get file info from Telegram');
            }

            $telegramFilePath = $fileInfo['result']['file_path'];
            $downloadUrl = "https://api.telegram.org/file/bot{$token}/{$telegramFilePath}";

            // Ekstensi dan tipe MIME sementara
            $extension = pathinfo($telegramFilePath, PATHINFO_EXTENSION);
            $tempFileName = uniqid() . '.' . $extension;
            $tempPath = storage_path('app/temp/' . $tempFileName);

            // Pastikan direktori temp ada
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            // Download file dari Telegram
            $fileContent = file_get_contents($downloadUrl);
            if ($fileContent === false) {
                throw new \Exception('Failed to download file from Telegram');
            }

            file_put_contents($tempPath, $fileContent);

            // Ambil info file
            $mimeType = mime_content_type($tempPath);
            $fileSize = filesize($tempPath);

            // --- 🔍 Cek storage user sebelum menyimpan permanen ---
            $setting = $this->device->business; // asumsikan relasi ke business
            $storageCheck = $this->checkStorage($setting, $fileSize);

            if (!$storageCheck['available']) {
                Log::warning("Storage full for business ID {$setting->id}");
                unlink($tempPath);
                return;
            }

            // --- 📁 Tentukan folder berdasarkan MIME type ---
            $subFolder = $this->getSubFolderByMimeType($mimeType);
            $uploadPath = "folders/{$setting->id}/{$subFolder}/";
            $this->ensureDirectoryExists($uploadPath);

            $finalFileName = uniqid() . '.' . $extension;
            $finalPath = 'uploads/' . $uploadPath . $finalFileName;

            // Pindahkan file dari temp ke lokasi final
            rename($tempPath, public_path($finalPath));

            // Update data file
            $this->fileData['path'] = $finalPath;
            $this->fileData['type'] = $mimeType;
            $this->fileData['size'] = $fileSize;

            // Update message type berdasarkan MIME
            $this->updateMessageTypeFromMime($mimeType);

            Log::info("Telegram file stored: {$finalPath}");
        } catch (\Exception $e) {
            Log::error('Telegram file download error: ' . $e->getMessage());
            $this->fileData = []; // Reset file data jika error
        }
    }

    private function ensureDirectoryExists($path)
    {
        if (!file_exists('uploads/' . $path)) {
            mkdir('uploads/' . $path, 0755, true);
        }
    }

    private function getSubFolderByMimeType(string $mimeType): string
    {
        $mainType = explode('/', $mimeType)[0];

        switch ($mainType) {
            case 'image':
                return 'telegram-images';
            case 'video':
                return 'telegram-video';
            case 'audio':
                return 'telegram-audio';
            case 'application':
            case 'text':
            default:
                return 'telegram-document';
        }
    }

    public function checkStorage($setting, $fileSize = 0)
    {
        if ($setting->merchant) {
            $totalSize  = 0;
            $path       = "uploads/folders/{$setting->id}";

            if (Storage::disk('local')->exists($path)) {
                $files = Storage::disk('local')->allFiles($path);
                foreach ($files as $file) {
                    $totalSize += Storage::disk('local')->size($file);
                }
            }

            // Convert to MB
            $usedStorageMB  = round($totalSize / 1024 / 1024, 2);
            $fileSizeMB     = round($fileSize / 1024 / 1024, 2);

            // Get total storage
            $storageFromSubscribe   = $setting->package_active ? (int)$setting->package_active->storage : 0;
            $storageFromAddons      = $setting->package_active_storage ? (int)$setting->package_active_storage->storage : 0;
            $totalStorage           = $storageFromSubscribe + $storageFromAddons;

            // Check if storage is available
            $remainingStorage = $totalStorage - $usedStorageMB;

            return [
                'available'         => $totalStorage > 0 && ($usedStorageMB + $fileSizeMB) <= $totalStorage,
                'total_storage'     => $totalStorage,
                'used_storage'      => $usedStorageMB,
                'remaining_storage' => $remainingStorage,
                'file_size'         => $fileSizeMB,
                'has_package'       => $totalStorage > 0
            ];
        } else {
            return [
                'available'         => true,
                'total_storage'     => 9999999,
                'used_storage'      => 0,
                'remaining_storage' => 9999,
                'file_size'         => 9999,
                'has_package'       => 9999
            ];
        }
    }



    private function updateMessageTypeFromMime(string $mimeType): void
    {
        foreach ($this->typeMimeData as $type => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                $this->messageType = $type;
                break;
            }
        }
    }
}
