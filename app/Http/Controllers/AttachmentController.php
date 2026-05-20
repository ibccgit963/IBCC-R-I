<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Dispatch;
use App\Models\CourierAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'           => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
            'attachable_type'=> 'required|in:courier,dispatch',
            'attachable_id'  => 'required|integer',
        ]);

        $type = $request->attachable_type;
        $id   = $request->attachable_id;

        if ($type === 'courier') {
            $model = Courier::findOrFail($id);
            $this->authorizeModel($model);
        } else {
            $model = Dispatch::findOrFail($id);
            $this->authorizeDispatch($model);
        }

        $file       = $request->file('file');
        $storedName = $file->store("attachments/{$type}s", 'public');

        $model->attachments()->create([
            'original_name' => $file->getClientOriginalName(),
            'stored_name'   => $storedName,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'uploaded_by'   => Auth::id(),
        ]);

        return back()->with('success', 'File attached successfully.');
    }

    public function destroy(CourierAttachment $attachment)
    {
        $user = Auth::user();
        if ($attachment->uploaded_by !== $user->id && !in_array($user->role->slug, ['super-admin', 'center-admin'])) {
            abort(403);
        }

        Storage::disk('public')->delete($attachment->stored_name);
        $attachment->delete();

        return back()->with('success', 'Attachment deleted.');
    }

    private function authorizeModel(Courier $courier): void
    {
        $user = Auth::user();
        if ($user->role->slug === 'super-admin') return;
        if ($courier->center_id !== $user->center_id) abort(403);
    }

    private function authorizeDispatch(Dispatch $dispatch): void
    {
        $user = Auth::user();
        if ($user->role->slug === 'super-admin') return;
        if ($dispatch->center_id !== $user->center_id) abort(403);
    }
}
