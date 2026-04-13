<?php

namespace Modules\QcComplaintSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Modules\QcComplaintSystem\Models\QcFinding;
use Modules\QcComplaintSystem\Services\QcFindingService;

class QcPicDeadlineInboxController extends Controller
{
    public function __construct(
        protected QcFindingService $findingService
    ) {}

    public function index(): View
    {
        $authUserId = (int) auth()->id();
        $deadlines = $this->findingService->pendingDeadlineInboxForUser($authUserId);
        $completions = $this->findingService->pendingCompletionInboxForUser($authUserId);

        $allItems = $deadlines->getCollection()->concat($completions->getCollection());

        $picIds = $allItems
            ->flatMap(function (QcFinding $finding) {
                $ids = array_map('intval', (array) ($finding->pic_user_ids ?? []));
                if (!empty($finding->pic_user_id)) {
                    $ids[] = (int) $finding->pic_user_id;
                }

                return $ids;
            })
            ->filter()
            ->unique()
            ->values();

        $picNameMap = User::query()
            ->whereIn('id', $picIds)
            ->pluck('name', 'id')
            ->toArray();

        $canSetDeadlineMap = [];
        foreach ($deadlines->getCollection() as $finding) {
            $canSetDeadlineMap[$finding->id] = $this->findingService->userCanSetDeadline($finding, $authUserId);
        }

        $canSubmitCompletionMap = [];
        foreach ($completions->getCollection() as $finding) {
            $canSubmitCompletionMap[$finding->id] = $this->findingService->userCanSubmitCompletion($finding, $authUserId);
        }

        return view('qccomplaintsystem::deadlines.index', [
            'deadlines' => $deadlines,
            'completions' => $completions,
            'picNameMap' => $picNameMap,
            'canSetDeadlineMap' => $canSetDeadlineMap,
            'canSubmitCompletionMap' => $canSubmitCompletionMap,
        ]);
    }
}
