<?php

namespace Modules\SystemSupport\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SystemSupport\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    private function canManageAnnouncements(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole(['Admin', 'Admin IT', 'Helpdesk']);
    }

    public function index()
    {
        if (! $this->canManageAnnouncements()) {
            abort(403, 'Akses ditolak.');
        }

        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('systemsupport::announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        if (! $this->canManageAnnouncements()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:info,warning,maintenance,update',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        Announcement::create([
            'title' => $request->title,
            'type' => $request->type,
            'content' => $request->content,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        \Illuminate\Support\Facades\Cache::forget('global_announcements');

        return redirect()->back()->with('success', 'Pengumuman Sistem berhasil ditambahkan.');
    }

    public function toggleActive($id)
    {
        if (! $this->canManageAnnouncements()) {
            abort(403);
        }

        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'is_active' => !$announcement->is_active
        ]);

        \Illuminate\Support\Facades\Cache::forget('global_announcements');

        return redirect()->back()->with('success', 'Status pengumuman diubah.');
    }

    public function destroy($id)
    {
        if (! $this->canManageAnnouncements()) {
            abort(403);
        }

        Announcement::findOrFail($id)->delete();
        \Illuminate\Support\Facades\Cache::forget('global_announcements');
        return redirect()->back()->with('success', 'Pengumuman dihapus secara permanen.');
    }
}
