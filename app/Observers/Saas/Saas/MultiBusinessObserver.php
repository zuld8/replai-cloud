<?php

namespace App\Observers\Saas\Saas;

use App\Models\Blash\BlashWhatsapp;
use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChat;
use App\Models\LiveChat;
use App\Models\Log;
use App\Models\Master\Category;
use App\Models\Master\MessageTemplate;
use App\Models\Media\Folder;
use App\Models\Meta\InstagramAccount;
use App\Models\Meta\MessengerAccount;
use App\Models\MetaAccount;
use App\Models\Setting;
use App\Models\Store\Scrapping;
use App\Models\Store\Store;
use App\Models\TelegramKey;
use App\Models\Ticket\Ticket;
use App\Models\WhatsappDevice;

class MultiBusinessObserver
{
    public function deleting(Setting $setting)
    {
        BlashWhatsapp::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        HistoryChat::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        LiveChat::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        TelegramKey::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        Scrapping::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        WhatsappDevice::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        MetaAccount::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        ChatBot::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        FineTunnel::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        Store::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        Folder::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        Log::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        Ticket::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        MessengerAccount::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        InstagramAccount::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        MessageTemplate::withoutGlobalScopes()->where('business_id', $setting->id)->delete();
        Category::withoutGlobalScopes()->where('business_id', $setting->id)->delete();

      
        $businessId = $setting->id; 

        // Security check
        $basePath = 'uploads/folders/' . $businessId;
        $fullPath = $basePath;

        if (strpos($fullPath, $basePath) !== 0) { 
        }

        $folderPathAbsolute = public_path($fullPath);
        $basePathAbsolute = public_path($basePath);

        $realFolderPath = realpath($folderPathAbsolute);
        $realBasePath = realpath($basePathAbsolute);

        // Prevent deleting base directory
        if ($realFolderPath === $realBasePath) { 
        }

        // Pastikan folder ada dalam base directory
        if (!$realFolderPath || strpos($realFolderPath, $realBasePath) !== 0) { 
        }

        if (file_exists($folderPathAbsolute) && is_dir($folderPathAbsolute)) { 
            $this->deleteDirectory($folderPathAbsolute); 
        }
 
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
}
