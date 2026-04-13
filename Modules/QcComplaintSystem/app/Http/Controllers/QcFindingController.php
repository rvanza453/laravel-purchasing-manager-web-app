<?php

namespace Modules\QcComplaintSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\ServiceAgreementSystem\Models\Block;
use Modules\ServiceAgreementSystem\Models\Department;
use Modules\ServiceAgreementSystem\Models\Site;
use Modules\ServiceAgreementSystem\Models\SubDepartment;
use Modules\QcComplaintSystem\Http\Requests\ApproveQcFindingCompletionRequest;
use Modules\QcComplaintSystem\Http\Requests\RejectQcFindingCompletionRequest;
use Modules\QcComplaintSystem\Http\Requests\SetQcFindingDeadlineRequest;
use Modules\QcComplaintSystem\Http\Requests\StoreQcFindingRequest;
use Modules\QcComplaintSystem\Http\Requests\SubmitQcFindingCompletionRequest;
use Modules\QcComplaintSystem\Http\Requests\UpdateQcFindingRequest;
use Modules\QcComplaintSystem\Models\QcFinding;
use Modules\QcComplaintSystem\Services\QcFindingService;

class QcFindingController extends Controller
{
    public function __construct(
        protected QcFindingService $findingService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $isHoUser = $this->isHoUser($user);
        $forcedSiteId = $isHoUser ? null : (int) ($user?->site_id ?? 0);
        $statusProvided = $request->has('status');

        $siteFilter = $request->get('site_id');
        if ($forcedSiteId) {
            $siteFilter = $forcedSiteId;
        }

        $filters = [
            'status' => $request->get('status'),
            'urgency' => $request->get('urgency'),
            'kategori' => $request->get('kategori'),
            'sub_kategori' => $request->get('sub_kategori'),
            'site_id' => $siteFilter,
            'department_id' => $request->get('department_id'),
            'sub_department_id' => $request->get('sub_department_id'),
            'block_id' => $request->get('block_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'needs_resubmission' => $request->boolean('needs_resubmission') ? 1 : null,
            'keyword' => $request->get('keyword'),
            // Default listing shows only active findings until user explicitly picks a status filter.
            'exclude_closed' => $statusProvided ? null : 1,
        ];

        // Prevent cross-scope filtering by URL tampering for non-HO users.
        if ($forcedSiteId) {
            $filters['site_id'] = $forcedSiteId;
        }

        $findings = $this->findingService->paginate($filters);

        $picIds = $findings->getCollection()
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

        $sitesQuery = Site::query()->orderBy('name');
        if ($forcedSiteId) {
            $sitesQuery->where('id', $forcedSiteId);
        }
        $sites = $sitesQuery->get(['id', 'name']);

        $departmentsQuery = Department::query()->orderBy('name');
        if ($filters['site_id']) {
            $departmentsQuery->where('site_id', (int) $filters['site_id']);
        }
        $departments = $departmentsQuery->get(['id', 'name', 'site_id']);

        $subDepartments = collect();
        if (!empty($filters['department_id'])) {
            $subDepartments = SubDepartment::query()
                ->where('department_id', (int) $filters['department_id'])
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $blocks = collect();
        if (!empty($filters['sub_department_id'])) {
            $blocks = Block::query()
                ->where('sub_department_id', (int) $filters['sub_department_id'])
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('qccomplaintsystem::findings.index', [
            'findings' => $findings,
            'picNameMap' => $picNameMap,
            'filters' => $filters,
            'statusOptions' => QcFinding::statusOptions(),
            'urgencyOptions' => QcFinding::urgencyOptions(),
            'categoryOptions' => QcFinding::categoryOptions(),
            'sites' => $sites,
            'departments' => $departments,
            'subDepartments' => $subDepartments,
            'blocks' => $blocks,
            'isHoUser' => $isHoUser,
            'statusCounts' => $this->findingService->statusCounts($filters),
            'categoryBreakdown' => $this->findingService->categoryBreakdown($filters),
        ]);
    }

    public function summary(Request $request)
    {
        $user = auth()->user();
        $isHoUser = $this->isHoUser($user);
        $forcedSiteId = $isHoUser ? null : (int) ($user?->site_id ?? 0);

        $sitesQuery = Site::query()->orderBy('name');
        if ($forcedSiteId) {
            $sitesQuery->where('id', $forcedSiteId);
        }
        $sites = $sitesQuery->get(['id', 'name']);

        $selectedSiteId = $forcedSiteId ?: ($request->filled('site_id') ? (int) $request->get('site_id') : null);
        if ($selectedSiteId && !$sites->contains('id', $selectedSiteId)) {
            $selectedSiteId = $forcedSiteId ?: null;
        }

        $siteSummaryFilters = ['exclude_closed' => 1];
        if ($forcedSiteId) {
            $siteSummaryFilters['site_id'] = $forcedSiteId;
        }

        $departmentSummaryFilters = ['exclude_closed' => 1];
        if ($selectedSiteId) {
            $departmentSummaryFilters['site_id'] = $selectedSiteId;
        }

        $siteSummary = $this->findingService->summaryBySite($siteSummaryFilters);
        $departmentSummary = $this->findingService->summaryByDepartment($departmentSummaryFilters);

        return view('qccomplaintsystem::dashboard.summary', [
            'sites' => $sites,
            'isHoUser' => $isHoUser,
            'selectedSiteId' => $selectedSiteId,
            'siteSummary' => $siteSummary,
            'departmentSummary' => $departmentSummary,
            'totalActiveFindings' => (int) $siteSummary->sum('total_findings'),
            'totalOpenFindings' => (int) $siteSummary->sum('open_total'),
            'totalInReviewFindings' => (int) $siteSummary->sum('in_review_total'),
        ]);
    }

    public function create()
    {
        $authUser = auth()->user();

        if (!$this->canCreateFinding($authUser)) {
            return redirect()->route('qc.findings.index')
                ->with('error', 'Hanya staff dari HO dan QC yang bisa menambahkan temuan.');
        }

        return view('qccomplaintsystem::findings.create', [
            'authUser' => $authUser,
            'users' => $this->activeUsers(),
            'sites' => Site::query()->orderBy('name')->get(['id', 'name']),
            'departments' => $this->departmentsForUserScope($authUser),
            'subDepartments' => collect(),
            'blocks' => collect(),
            'urgencyOptions' => QcFinding::urgencyOptions(),
            'sourceOptions' => QcFinding::sourceOptions(),
            'categoryOptions' => QcFinding::categoryOptions(),
        ]);
    }

    public function store(StoreQcFindingRequest $request)
    {
        try {
            $payload = $this->normalizeSourceTypePayload($request->validated());
            $payload = $this->resolveLocationFromManualInput($payload);
            $finding = $this->findingService->create($payload, (int) auth()->id());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('qc.findings.show', $finding)
            ->with('success', 'Temuan QC berhasil dibuat.');
    }

    public function show(Request $request, QcFinding $finding)
    {
        if ($request->filled('attachment_view')) {
            return $this->viewFindingAttachment($finding, (int) $request->query('attachment_view'));
        }

        if ($request->filled('attachment_download')) {
            return $this->downloadFindingAttachment($finding, (int) $request->query('attachment_download'));
        }

        if ($request->filled('evidence_view')) {
            return $this->viewCompletionEvidence($finding, (int) $request->query('evidence_view'));
        }

        if ($request->filled('evidence_download')) {
            return $this->downloadCompletionEvidence($finding, (int) $request->query('evidence_download'));
        }

        $finding = $this->findingService->findById($finding->id);
        $authId = (int) auth()->id();

        $picIds = collect(array_map('intval', (array) ($finding->pic_user_ids ?? [])));
        if (!empty($finding->pic_user_id)) {
            $picIds->push((int) $finding->pic_user_id);
        }

        $picNameMap = User::query()
            ->whereIn('id', $picIds->filter()->unique()->values())
            ->pluck('name', 'id')
            ->toArray();

        return view('qccomplaintsystem::findings.show', [
            'finding' => $finding,
            'picNameMap' => $picNameMap,
            'canSubmitCompletion' => $this->findingService->userCanSubmitCompletion($finding, $authId),
            'canSetDeadline' => $this->findingService->userCanSetDeadline($finding, $authId),
            'canApproveCompletion' => $this->findingService->userCanApproveFinding($finding, $authId),
            'currentApprovalStep' => $finding->currentPendingApprovalStep(),
        ]);
    }

    public function edit(QcFinding $finding)
    {
        $finding = $this->findingService->findById($finding->id);
        $authId  = (int) auth()->id();
        $authUser = auth()->user();

        // QC Officer may only edit findings they personally created
        if (!$authUser->hasModuleRole('qc', 'QC Admin')) {
            if ($finding->created_by !== $authId) {
                abort(403, 'Anda hanya dapat mengedit temuan yang Anda buat sendiri.');
            }
        }

        return view('qccomplaintsystem::findings.edit', [
            'finding' => $finding,
            'authUser' => $authUser,
            'users' => $this->activeUsers(),
            'sites' => Site::query()->orderBy('name')->get(['id', 'name']),
            'departments' => $this->departmentsForUserScope($authUser),
            'subDepartments' => SubDepartment::query()
                ->where('department_id', $finding->department_id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'blocks' => Block::query()
                ->where('sub_department_id', $finding->sub_department_id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'urgencyOptions' => QcFinding::urgencyOptions(),
            'sourceOptions' => QcFinding::sourceOptions(),
            'categoryOptions' => QcFinding::categoryOptions(),
        ]);
    }

    public function getSubDepartments(int $departmentId)
    {
        $query = SubDepartment::query()
            ->where('department_id', $departmentId);

        $authUser = auth()->user();
        if (!$this->isHoUser($authUser) && !empty($authUser?->site_id)) {
            $query->whereHas('department', function ($builder) use ($authUser) {
                $builder->where('site_id', (int) $authUser->site_id);
            });
        }

        $subDepartments = $query->orderBy('name')->get(['id', 'name']);

        return response()->json($subDepartments);
    }

    public function getBlocks(int $subDepartmentId)
    {
        $query = Block::query()
            ->where('sub_department_id', $subDepartmentId);

        $authUser = auth()->user();
        if (!$this->isHoUser($authUser) && !empty($authUser?->site_id)) {
            $query->whereHas('subDepartment.department', function ($builder) use ($authUser) {
                $builder->where('site_id', (int) $authUser->site_id);
            });
        }

        $blocks = $query->orderBy('name')->get(['id', 'name']);

        return response()->json($blocks);
    }

    public function update(UpdateQcFindingRequest $request, QcFinding $finding)
    {
        $authId   = (int) auth()->id();
        $authUser = auth()->user();

        // QC Officer may only update findings they personally created
        if (!$authUser->hasModuleRole('qc', 'QC Admin')) {
            if ($finding->created_by !== $authId) {
                abort(403, 'Anda hanya dapat mengedit temuan yang Anda buat sendiri.');
            }
        }

        try {
            $payload = $this->normalizeSourceTypePayload($request->validated());
            $payload = $this->resolveLocationFromManualInput($payload);
            $this->findingService->update($finding, $payload, (int) auth()->id());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('qc.findings.show', $finding)
            ->with('success', 'Temuan QC berhasil diperbarui.');
    }

    public function destroy(QcFinding $finding)
    {
        return redirect()->route('qc.findings.index')
            ->with('error', 'Fitur hapus tidak diaktifkan untuk menjaga histori audit temuan.');
    }

    public function submitCompletion(SubmitQcFindingCompletionRequest $request, QcFinding $finding)
    {
        try {
            $this->findingService->submitCompletion($finding, $request->validated(), (int) auth()->id());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('qc.findings.show', $finding)
            ->with('success', 'Bukti penyelesaian berhasil dikirim dan menunggu approval.');
    }

    public function setDeadline(SetQcFindingDeadlineRequest $request, QcFinding $finding)
    {
        try {
            $this->findingService->setDeadlineForFinding($finding, $request->validated(), (int) auth()->id());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('qc.findings.show', $finding)
            ->with('success', 'Deadline penyelesaian berhasil disimpan. PIC dapat segera menyiapkan upload bukti penyelesaian.');
    }

    public function approveCompletion(ApproveQcFindingCompletionRequest $request, QcFinding $finding)
    {
        try {
            $updated = $this->findingService->approveCompletion(
                $finding,
                (int) auth()->id(),
                $request->validated()['approval_note'] ?? null
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('qc.findings.show', $finding)
            ->with('success', $updated->status === QcFinding::STATUS_CLOSED
                ? 'Approval level final selesai. Temuan ditutup (closed).'
                : 'Approval level Anda berhasil. Menunggu approval level berikutnya.');
    }

    public function rejectCompletion(RejectQcFindingCompletionRequest $request, QcFinding $finding)
    {
        try {
            $this->findingService->rejectCompletion(
                $finding,
                (int) auth()->id(),
                $request->validated()['rejected_note']
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('qc.findings.show', $finding)
            ->with('success', 'Penyelesaian ditolak. PIC wajib melengkapi dan submit ulang bukti penyelesaian.');
    }

    public function viewFindingAttachment(QcFinding $finding, int $index)
    {
        $finding = $this->findingService->findById($finding->id);
        $attachments = (array) ($finding->finding_attachments ?? []);
        $path = $attachments[$index] ?? null;

        $resolvedPath = $this->resolveQcStoredFilePath($path, 'qc-findings/reporting');
        if (!$resolvedPath) {
            return back()->with('error', 'Lampiran temuan tidak ditemukan di server.');
        }

        return response()->file($resolvedPath);
    }

    public function downloadFindingAttachment(QcFinding $finding, int $index)
    {
        $finding = $this->findingService->findById($finding->id);
        $attachments = (array) ($finding->finding_attachments ?? []);
        $path = $attachments[$index] ?? null;

        $resolvedPath = $this->resolveQcStoredFilePath($path, 'qc-findings/reporting');
        if (!$resolvedPath) {
            return back()->with('error', 'Lampiran temuan tidak ditemukan di server.');
        }

        $ext = pathinfo($resolvedPath, PATHINFO_EXTENSION);
        $downloadName = 'qc-finding-' . str_replace('/', '-', (string) $finding->finding_number) . '-attachment-' . ($index + 1) . ($ext ? '.' . $ext : '');

        return response()->download($resolvedPath, $downloadName);
    }

    public function viewCompletionEvidence(QcFinding $finding, int $evidence)
    {
        $finding = $this->findingService->findById($finding->id);
        $evidenceModel = $finding->completionEvidences->firstWhere('id', $evidence);
        $resolvedPath = $this->resolveQcStoredFilePath($evidenceModel?->file_path, 'qc-findings/completions');

        if (!$resolvedPath) {
            return back()->with('error', 'Bukti penyelesaian tidak ditemukan di server.');
        }

        return response()->file($resolvedPath);
    }

    public function downloadCompletionEvidence(QcFinding $finding, int $evidence)
    {
        $finding = $this->findingService->findById($finding->id);
        $evidenceModel = $finding->completionEvidences->firstWhere('id', $evidence);
        $resolvedPath = $this->resolveQcStoredFilePath($evidenceModel?->file_path, 'qc-findings/completions');

        if (!$resolvedPath) {
            return back()->with('error', 'Bukti penyelesaian tidak ditemukan di server.');
        }

        $ext = pathinfo($resolvedPath, PATHINFO_EXTENSION);
        $baseName = pathinfo((string) ($evidenceModel?->original_name ?? ''), PATHINFO_FILENAME);
        $downloadName = ($baseName ?: ('qc-evidence-' . $evidence)) . ($ext ? '.' . $ext : '');

        return response()->download($resolvedPath, $downloadName);
    }

    private function activeUsers()
    {
        return User::query()->orderBy('name')->get(['id', 'name']);
    }

    private function departmentsForUserScope(?User $user)
    {
        $query = Department::query()->orderBy('name');

        if (!$this->isHoUser($user) && !empty($user?->site_id)) {
            $query->where('site_id', (int) $user->site_id);
        }

        return $query->get(['id', 'name', 'site_id']);
    }

    private function isHoUser(?User $user): bool
    {
        $siteName = strtolower((string) $user?->site?->name);

        return in_array($siteName, ['head office', 'ho'], true);
    }

    private function canCreateFinding(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        $siteName = strtolower(trim((string) $user->site?->name));
        $position = strtolower(trim((string) $user->position));

        $isHoSite = str_contains($siteName, 'head office')
            || (bool) preg_match('/\bho\b/i', $siteName);
        $isQcPosition = str_contains($position, 'qc');

        return $isHoSite || $isQcPosition;
    }

    private function resolveQcStoredFilePath(?string $rawPath, ?string $fallbackFolder = null): ?string
    {
        $raw = trim((string) $rawPath);
        if ($raw === '') {
            return null;
        }

        $normalized = str_replace('\\\\', '/', $raw);
        $normalized = ltrim($normalized, '/');
        $withoutPublicPrefix = preg_replace('/^public\//', '', $normalized);

        $candidates = array_values(array_unique(array_filter([
            $normalized,
            $withoutPublicPrefix,
            'public/' . $withoutPublicPrefix,
            $fallbackFolder ? ($fallbackFolder . '/' . basename($withoutPublicPrefix)) : null,
        ])));

        foreach ($candidates as $candidate) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->path($candidate);
            }

            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($candidate)) {
                return \Illuminate\Support\Facades\Storage::disk('local')->path($candidate);
            }

            $directPublicPath = storage_path('app/public/' . $candidate);
            if (is_file($directPublicPath)) {
                return $directPublicPath;
            }

            $directLocalPath = storage_path('app/' . $candidate);
            if (is_file($directLocalPath)) {
                return $directLocalPath;
            }
        }

        return null;
    }

    private function normalizeSourceTypePayload(array $payload): array
    {
        if (($payload['source_type'] ?? null) === 'other') {
            $payload['source_type'] = trim((string) ($payload['source_type_custom'] ?? ''));
        }

        unset($payload['source_type_custom']);

        return $payload;
    }

    private function resolveLocationFromManualInput(array $payload): array
    {
        $subDepartmentId = (int) ($payload['sub_department_id'] ?? 0);
        $blockName = trim((string) ($payload['block_name'] ?? ''));

        $subDepartment = SubDepartment::query()->findOrFail($subDepartmentId);

        $block = Block::query()->firstOrCreate(
            [
                'sub_department_id' => $subDepartment->id,
                'name' => $blockName,
            ],
            [
                'code' => null,
                'is_active' => true,
            ]
        );

        $payload['block_id'] = $block->id;

        unset($payload['block_name']);

        return $payload;
    }
}
