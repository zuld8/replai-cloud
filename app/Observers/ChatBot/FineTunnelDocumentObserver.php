<?php

namespace App\Observers\ChatBot;

use App\Models\ChatBot\FineTunnelDocument;

class FineTunnelDocumentObserver
{
    public function deleting(FineTunnelDocument $document)
    {
        $this->unlinkFile($document->file_path);

        // Delete all extracted images
        $chunks = $document->chunks()->whereNotNull('image_path')->get();
        foreach ($chunks as $chunk) {
            $this->unlinkFile($chunk->image_path);
        }
    }

    public function unlinkFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
