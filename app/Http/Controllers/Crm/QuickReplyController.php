<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Resources\Crm\QuickReplyListResource;
use App\Models\ChatBot\QuickReply;
use App\Observers\ChatBot\QuickReplyObserver;
use Illuminate\Http\Request;

class QuickReplyController extends Controller
{
    protected $quickReplyObserver;

    public function __construct(QuickReplyObserver $quickReplyObserver)
    {
        $this->quickReplyObserver       = $quickReplyObserver;
    }

    public function show(Request $request)
    {
        $limit      = $request->limit ? $request->limit : 50;
        $data       = $this->quickReplyObserver->getData($request);

        $totalRows      = $data->count();
        $contacts       = $data->paginate($limit);

        return response()->json([
            'total'         => $totalRows,
            'data'          => QuickReplyListResource::collection($contacts),
        ], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx|max:5000',
        ]);

        $media = null;
        if ($request->hasFile('media')) {
            $media = $this->uploadImage($request, 'media', 'media-manager', true);
        }

        $quickReply = QuickReply::create([
            'name'          => $request->name,
            'content'       => $request->content,
            'media_url'     => $media['file_path'] ?? null,
            'file_type'     => $media['file_type'] ?? null,
            'type'          => $media['type'] ?? 'text',
            'file_name'     => $media['file_name'] ?? null,
            'file_size'     => $media['file_size'] ?? null,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Quick reply berhasil dibuat',
            'data'      => QuickReplyListResource::make($quickReply),
        ]);
    }

    public function update(Request $request, QuickReply $quickly)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx|max:5000',
        ]);

        $media = null;
        if ($request->hasFile('media')) {
            $media = $this->uploadImage($request, 'media', 'media-manager', true);
        }

        $quickly->update([
            'name'          => $request->name,
            'content'       => $request->content,
            'media_url'     => $media != null ? ($media['file_path'] ?? null) : $quickly->media_url,
            'file_type'     => $media != null ? ($media['file_type'] ?? null) : $quickly->file_type,
            'type'          => $media != null ? ($media['type'] ?? null) : $quickly->type,
            'file_name'     => $media != null ? ($media['file_name'] ?? null) : $quickly->file_name,
            'file_size'     => $media != null ? ($media['file_size'] ?? null) : $quickly->file_size,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Quick reply berhasil dibuat',
            'data'      => QuickReplyListResource::make($quickly),
        ]);
    }

    public function remove(QuickReply $quickly)
    {
        // Hapus file media jika ada
        $this->unlinkFile($quickly->media_url);
        $quickly->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Quick reply berhasil di hapus'
        ]);
    }
}
