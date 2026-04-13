<?php

namespace App\Http\Controllers\Admin;

use Modules\PrSystem\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends AdminController
{
    public function index(): View
    {
        $activities = ActivityLog::with(['user'])
                    ->latest()
                    ->paginate(50);

        return view('admin.activity-logs.index', compact('activities'));
    }
}
