<?php

namespace App\Observers\Media;

use App\Models\Media\MediaContent;
use Illuminate\Http\Request;

class MediaObserver
{
    public function createData(Request $request, $name, $path,$format)
    {
        MediaContent::create([
            'format'    => $format,
            'name'      => $name,
            'path'      => $path,
            'folder_id' => $request->folder_id
        ]);
    }
}
