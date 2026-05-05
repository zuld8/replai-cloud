<?php

namespace App\Process\TemplateEditor;
use Illuminate\Support\Facades\Storage; 

class AssetEmailTemplateRepository
{
    protected $diskPath;
    protected $storage;

    public function __construct()
    {
        $this->storage  = Storage::disk(config('editor.assets.disk'));
        $this->diskPath = config('editor.assets.path') ?? 'uploads/custom-mail';
    }

    public function getAllMediaLinks()
    {
        return collect($this->storage->allFiles($this->diskPath))
            ->map(fn($file) => asset($file))
            ->toArray();
    } 
}
