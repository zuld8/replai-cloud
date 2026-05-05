<?php

namespace App\Observers\Media;

use App\Models\Media\Folder;
use Illuminate\Http\Request;

class FolderObserver
{
    public function createData(Request $request)
    {
        return  Folder::create([
            'name'      => $request->name,
            'folder_id' => $request->folder_id
        ]);
    }

    public function deleting(Folder $folder)
    {
        foreach ($folder->media as $media) {
            $this->unlinkFile($media->path);
            $media->delete();
        }

        foreach ($folder->subs as $subFolder) {
            $this->deleting($subFolder);
        } 
    }

    public function unlinkFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
