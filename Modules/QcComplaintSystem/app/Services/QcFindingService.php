<?php

namespace Modules\QcComplaintSystem\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\QcComplaintSystem\Models\QcFinding;
use Modules\QcComplaintSystem\Models\QcFindingApprovalStep;
use Modules\QcComplaintSystem\Repositories\QcFindingRepository;

class QcFindingService
{
    public function __construct(
        protected QcFindingRepository $repository,
        protected QcApprovalConfigService $approvalConfigService
    ) {}

    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters);
    }

    public function statusCounts(array $filters = []): array
    {
        return $this->repository->statusCounts($filters);
    }

    public function categoryBreakdown(array $filters = []): array
    {
        return $this->repository->categoryBreakdown($filters);
    }

    public function summaryBySite(array $filters = [])
    {
        return $this->repository->summaryBySite($filters);
    }

    public function summaryByDepartment(array $filters = [])
    {
        return $this->repository->summaryByDepartment($filters);
    }

    public function findById(int $id): QcFinding
    {
        return $this->repository->findById($id);
    }

    public function pendingApprovalsForUser(int $userId): LengthAwarePaginator
    {
        $query = QcFindingApprovalStep::query()
            ->with([
                'approver:id,name',
                'finding.department:id,name',
                'finding.subDepartment:id,name',
                'finding.block:id,name',
                'finding.reporter:id,name',
                'finding.approvalSteps',
            ])
            ->where('approver_user_id', $userId)
            ->where('status', QcFindingApprovalStep::STATUS_PENDING)
            ->whereHas('finding', function ($builder) {
                $builder->where('status', QcFinding::STATUS_IN_REVIEW);
            })
            ->orderBy('created_at');

        return $query->get()
            ->filter(fn (QcFindingApprovalStep $step) => (int) ($step->finding?->currentPendingApprovalStep()?->id ?? 0) === (int) $step->id)
            ->values()
            ->pipe(function ($collection) {
                $page = request()->integer('page', 1);
                $perPage = 12;
                $total = $collection->count();
                $items = $collection->slice(($page - 1) * $perPage, $perPage)->values();

                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $total,
                    $perPage,
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            });
    }

    public function pendingDeadlineInboxForUser(int $userId): LengthAwarePaginator
    {
        $query = $this->basePicInboxQuery($userId)
            ->whereNull('target_resolution_date')
            ->latest('id');

        return $query->paginate(8, ['*'], 'deadline_page')->withQueryString();
    }

    public function pendingCompletionInboxForUser(int $userId): LengthAwarePaginator
    {
        $query = $this->basePicInboxQuery($userId)
            ->whereNotNull('target_resolution_date')
            ->latest('id');

        return $query->paginate(8, ['*'], 'completion_page')->withQueryString();
    }

    public function create(array $data, int $createdBy): QcFinding
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $payload = $this->normalizeFindingPayload($data, $createdBy);
            $payload['finding_number'] = $this->generateFindingNumber();
            
            $attachments = $data['finding_attachments'] ?? [];
            if (!is_array($attachments)) {
                $attachments = [$attachments];
            }
            $payload['finding_attachments'] = $this->storeFindingAttachments($attachments);

            return $this->repository->create($payload);
        });
    }

    public function update(QcFinding $finding, array $data, int $updatedBy): QcFinding
    {
        if (!$finding->isOpen()) {
            throw new \RuntimeException('Temuan yang sudah closed tidak dapat diubah.');
        }

        return DB::transaction(function () use ($finding, $data, $updatedBy) {
            $payload = $this->normalizeFindingPayload($data, $updatedBy, false);
            $existingAttachments = array_values(array_filter((array) ($finding->finding_attachments ?? [])));
            $removeAttachments = array_values(array_filter((array) ($data['finding_attachments_remove'] ?? [])));
            $removeAttachments = array_values(array_intersect($existingAttachments, $removeAttachments));
            $keepAttachments = array_values(array_diff($existingAttachments, $removeAttachments));

            foreach ($removeAttachments as $path) {
                Storage::disk('public')->delete($path);
            }

            $mergedAttachments = $keepAttachments;
            if (!empty($data['finding_attachments'])) {
                $attachments = is_array($data['finding_attachments']) ? $data['finding_attachments'] : [$data['finding_attachments']];
                $mergedAttachments = array_values(array_merge(
                    $keepAttachments,
                    $this->storeFindingAttachments($attachments)
                ));
            }

            $payload['finding_attachments'] = $mergedAttachments;

            return $this->repository->update($finding, $payload);
        });
    }

    public function submitCompletion(QcFinding $finding, array $data, int $userId): QcFinding
    {
        if (!$finding->isOpen()) {
            throw new \RuntimeException('Temuan sudah closed.');
        }

        if (!$this->userCanSubmitCompletion($finding, $userId)) {
            throw new \RuntimeException('Hanya PIC yang ditunjuk atau pembuat temuan yang dapat submit penyelesaian.');
        }

        if (is_null($finding->target_resolution_date)) {
            throw new \RuntimeException('Deadline penyelesaian belum diisi. Salah satu PIC wajib mengisi deadline terlebih dahulu.');
        }

        return DB::transaction(function () use ($finding, $data, $userId) {
            $approverUserIds = $this->approvalConfigService->getApproverIdsForDepartment((int) $finding->department_id);

            if (empty($approverUserIds)) {
                throw new \RuntimeException('Konfigurasi approver untuk department temuan ini belum diatur. Silakan isi Approval Config per department terlebih dahulu.');
            }

            foreach ($finding->completionEvidences as $evidence) {
                Storage::disk('public')->delete($evidence->file_path);
            }
            $finding->completionEvidences()->delete();

            foreach (($data['completion_files'] ?? []) as $file) {
                if (!$file instanceof UploadedFile) {
                    continue;
                }

                $storedPath = $file->store('qc-findings/completions', 'public');
                if (!$storedPath || !Storage::disk('public')->exists($storedPath)) {
                    throw new \RuntimeException('Upload bukti penyelesaian gagal disimpan di server.');
                }
                $finding->completionEvidences()->create([
                    'file_path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => (int) $file->getSize(),
                    'uploaded_by' => $userId,
                ]);
            }

            $updated = $this->repository->update($finding, [
                'completion_note' => $data['completion_note'],
                'completion_photo_path' => null,
                'completion_submitted_by' => $userId,
                'completion_submitted_at' => now(),
                'completion_approved_by' => null,
                'completion_approved_at' => null,
                'completion_approval_note' => null,
                'completion_rejected_note' => null,
                'needs_resubmission' => false,
                'status' => QcFinding::STATUS_IN_REVIEW,
                'closed_at' => null,
                'updated_by' => $userId,
            ]);

            $updated->approvalSteps()->delete();

            foreach ($approverUserIds as $index => $approverUserId) {
                $updated->approvalSteps()->create([
                    'level' => $index + 1,
                    'approver_user_id' => $approverUserId,
                    'status' => QcFindingApprovalStep::STATUS_PENDING,
                ]);
            }

            return $updated->fresh(['approvalSteps.approver', 'approvalSteps.actor']);
        });
    }

    public function approveCompletion(QcFinding $finding, int $approverUserId, ?string $approvalNote = null): QcFinding
    {
        if (!$finding->hasPendingCompletionApproval()) {
            throw new \RuntimeException('Tidak ada penyelesaian yang menunggu approval.');
        }

        $finding->loadMissing('approvalSteps');
        $currentStep = $finding->currentPendingApprovalStep();

        if (!$currentStep) {
            throw new \RuntimeException('Tahapan approval tidak ditemukan untuk temuan ini.');
        }

        if (!$this->canApproveStep($finding, $currentStep, $approverUserId)) {
            throw new \RuntimeException('Anda tidak memiliki otorisasi approval untuk temuan ini.');
        }

        $currentStep->update([
            'status' => QcFindingApprovalStep::STATUS_APPROVED,
            'note' => $approvalNote,
            'acted_by' => $approverUserId,
            'acted_at' => now(),
        ]);

        $nextStep = $finding->approvalSteps()->pending()->orderBy('level')->first();

        if ($nextStep) {
            return $this->repository->update($finding, [
                'status' => QcFinding::STATUS_IN_REVIEW,
                'updated_by' => $approverUserId,
                'completion_rejected_note' => null,
            ]);
        }

        return $this->repository->update($finding, [
            'completion_approved_by' => $approverUserId,
            'completion_approved_at' => now(),
            'completion_approval_note' => $approvalNote ?: 'Semua level approval telah disetujui.',
            'completion_rejected_note' => null,
            'needs_resubmission' => false,
            'status' => QcFinding::STATUS_CLOSED,
            'closed_at' => now(),
            'updated_by' => $approverUserId,
        ]);
    }

    public function rejectCompletion(QcFinding $finding, int $approverUserId, string $rejectedNote): QcFinding
    {
        if (!$finding->hasPendingCompletionApproval()) {
            throw new \RuntimeException('Tidak ada penyelesaian yang menunggu approval.');
        }

        $finding->loadMissing('approvalSteps');
        $currentStep = $finding->currentPendingApprovalStep();

        if (!$currentStep) {
            throw new \RuntimeException('Tahapan approval tidak ditemukan untuk temuan ini.');
        }

        if (!$this->canApproveStep($finding, $currentStep, $approverUserId)) {
            throw new \RuntimeException('Anda tidak memiliki otorisasi approval untuk temuan ini.');
        }

        $currentStep->update([
            'status' => QcFindingApprovalStep::STATUS_REJECTED,
            'note' => $rejectedNote,
            'acted_by' => $approverUserId,
            'acted_at' => now(),
        ]);

        return $this->repository->update($finding, [
            'completion_approved_by' => null,
            'completion_approved_at' => null,
            'completion_approval_note' => null,
            'completion_rejected_note' => $rejectedNote,
            'needs_resubmission' => true,
            'completion_submitted_by' => null,
            'completion_submitted_at' => null,
            'status' => QcFinding::STATUS_OPEN,
            'closed_at' => null,
            'updated_by' => $approverUserId,
        ]);
    }

    public function userCanSubmitCompletion(QcFinding $finding, int $userId): bool
    {
        $user = User::find($userId);

        // Inbox PIC is role-agnostic within QC scope: assigned PIC may submit completion.
        if (!$user || !$user->hasModuleRole('qc', ['QC Admin', 'QC Officer', 'QC Approver'])) {
            return false;
        }

        // Admin can submit for any finding; other QC roles only for findings they own as PIC/creator.
        if ($user->hasModuleRole('qc', 'QC Admin')) {
            return true;
        }

        $picUserIds = $this->assignedPicIds($finding);

        return in_array($userId, $picUserIds, true) || $finding->created_by === $userId;
    }

    public function userCanSetDeadline(QcFinding $finding, int $userId): bool
    {
        $user = User::find($userId);

        // Inbox PIC is role-agnostic within QC scope: assigned PIC may set deadline.
        if (!$user || !$user->hasModuleRole('qc', ['QC Admin', 'QC Officer', 'QC Approver'])) {
            return false;
        }

        if ($user->hasModuleRole('qc', 'QC Admin')) {
            return true;
        }

        // Non-admin PIC can only set deadline once.
        if (!is_null($finding->target_resolution_date)) {
            return false;
        }

        return in_array($userId, $this->assignedPicIds($finding), true);
    }

    public function setDeadlineForFinding(QcFinding $finding, array $data, int $userId): QcFinding
    {
        $user = User::find($userId);

        if (!$this->userCanSetDeadline($finding, $userId)) {
            throw new \RuntimeException('Hanya PIC yang ditunjuk atau QC Admin yang dapat mengisi deadline.');
        }

        if (!is_null($finding->target_resolution_date) && !($user && $user->hasModuleRole('qc', 'QC Admin'))) {
            throw new \RuntimeException('Deadline sudah diset. Perubahan deadline selanjutnya hanya dapat dilakukan oleh QC Admin.');
        }

        if ($finding->status === QcFinding::STATUS_CLOSED) {
            throw new \RuntimeException('Temuan yang sudah closed tidak dapat diubah deadline-nya.');
        }

        $deadline = \Carbon\Carbon::parse($data['target_resolution_date'])->startOfDay();
        $findingDate = optional($finding->finding_date)->copy()?->startOfDay();
        $isLongTerm = $findingDate ? $findingDate->diffInDays($deadline, false) > 14 : false;

        return $this->repository->update($finding, [
            'target_resolution_date' => $deadline->toDateString(),
            'follow_up_plan' => trim((string) ($data['follow_up_plan'] ?? '')) ?: null,
            'is_long_term_case' => $isLongTerm,
            'updated_by' => $userId,
        ]);
    }

    public function userCanApprove(int $userId): bool
    {
        return $this->canApprove($userId);
    }

    public function userCanApproveFinding(QcFinding $finding, int $userId): bool
    {
        $finding->loadMissing('approvalSteps');
        $currentStep = $finding->currentPendingApprovalStep();

        if (!$currentStep) {
            return false;
        }

        return $this->canApproveStep($finding, $currentStep, $userId);
    }

    private function normalizeFindingPayload(array $data, int $actorId, bool $isCreate = true): array
    {
        $actor = User::query()->find($actorId);

        $kategori = $data['kategori'] ?? null;
        $subKategori = $data['sub_kategori'] ?? null;
        $kategoriCode = null;

        if ($kategori) {
            $hierarchy = QcFinding::categoryHierarchy();
            if (isset($hierarchy[$kategori])) {
                $categoryData = $hierarchy[$kategori];
                $kategoriCode = $categoryData['code'];

                if ($subKategori && isset($categoryData['subs'][$subKategori])) {
                    $kategoriCode = $categoryData['subs'][$subKategori]['code'];
                } else {
                    $subKategori = null;
                }
            } else {
                $kategori = null;
                $subKategori = null;
            }
        }

        $selectedPicIds = array_values(array_unique(array_filter(array_map('intval', (array) ($data['pic_user_ids'] ?? [])))));
        if (empty($selectedPicIds) && !empty($data['pic_user_id'])) {
            $selectedPicIds = [(int) $data['pic_user_id']];
        }

        $payload = [
            'finding_date' => $data['finding_date'] ?? now()->toDateString(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'source_type' => $data['source_type'],
            'department_id' => $data['department_id'],
            'sub_department_id' => $data['sub_department_id'],
            'block_id' => $data['block_id'],
            'location' => $data['location'] ?? null,
            'inspection_context' => $kategori === 'panen'
                ? $this->normalizeInspectionContext($data['inspection_context'] ?? [])
                : null,
            'finding_items' => $kategori === 'panen'
                ? $this->normalizeFindingItems($data['finding_items'] ?? [])
                : null,
            'urgency' => $data['urgency'],
            'kategori' => $kategori,
            'sub_kategori' => $subKategori,
            'kategori_code' => $kategoriCode,
            'reporter_user_id' => $actorId,
            'reporter_name' => $actor?->name,
            // Keep single PIC for backward compatibility and store full PIC list for new flow.
            'pic_user_id' => $selectedPicIds[0] ?? null,
            'pic_user_ids' => !empty($selectedPicIds) ? $selectedPicIds : null,
            'updated_by' => $actorId,
        ];

        if ($isCreate) {
            $payload['status'] = QcFinding::STATUS_OPEN;
            $payload['created_by'] = $data['created_by'] ?? $actorId;
        }

        return $payload;
    }

    private function normalizeInspectionContext(array $context): ?array
    {
        $normalized = [];

        foreach ([
            'total_ha_block',
            'sph',
            'abw',
            'alw',
            'inspection_date',
            'inspector_name',
            'assistant_witness',
            'mandor_witness',
        ] as $key) {
            if (!array_key_exists($key, $context)) {
                continue;
            }

            $value = $context[$key];
            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === '' || $value === null) {
                continue;
            }

            $normalized[$key] = $value;
        }

        return !empty($normalized) ? $normalized : null;
    }

    private function normalizeFindingItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $quantity = $item['quantity'] ?? null;
            if (is_string($quantity)) {
                $quantity = trim($quantity);
            }

            $normalized[] = [
                'template_key' => trim((string) ($item['template_key'] ?? '')) ?: null,
                'label' => $label,
                'quantity' => $quantity === '' || $quantity === null ? null : (float) $quantity,
                'note' => trim((string) ($item['note'] ?? '')) ?: null,
                'sort_order' => (int) $index + 1,
            ];
        }

        return array_values($normalized);
    }

    private function generateFindingNumber(): string
    {
        $prefix = 'QCF-' . now()->format('Ym');
        $lastFinding = QcFinding::query()
            ->where('finding_number', 'like', $prefix . '-%')
            ->latest('id')
            ->first();

        $lastSequence = 0;
        if ($lastFinding) {
            $parts = explode('-', $lastFinding->finding_number);
            $lastSequence = (int) end($parts);
        }

        return sprintf('%s-%04d', $prefix, $lastSequence + 1);
    }

    private function storeFindingAttachments(array $files, array $oldPaths = []): array
    {
        // If no new files uploaded, keep the old ones (if any)
        if (empty($files) || !collect($files)->contains(fn($f) => $f instanceof UploadedFile)) {
            return $oldPaths;
        }

        $newPaths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $storedPath = $file->store('qc-findings/reporting', 'public');
                if (!$storedPath || !Storage::disk('public')->exists($storedPath)) {
                    throw new \RuntimeException('Upload lampiran temuan gagal disimpan di server.');
                }

                $newPaths[] = $storedPath;
            }
        }

        return $newPaths;
    }

    private function canApprove(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // QC Admin overrides all — can approve at any level
        if ($user->hasModuleRole('qc', 'QC Admin')) {
            return true;
        }

        // QC Approver must also be explicitly listed in the approval config
        if ($user->hasModuleRole('qc', 'QC Approver')) {
            return $this->approvalConfigService->canApprove($userId);
        }

        return false;
    }

    private function assignedPicIds(QcFinding $finding): array
    {
        $picUserIds = array_values(array_unique(array_filter(array_map('intval', (array) ($finding->pic_user_ids ?? [])))));

        if ((int) ($finding->pic_user_id ?? 0) > 0) {
            $picUserIds[] = (int) $finding->pic_user_id;
        }

        return array_values(array_unique(array_filter($picUserIds)));
    }

    private function basePicInboxQuery(int $userId)
    {
        return QcFinding::query()
            ->with([
                'department:id,name',
                'subDepartment:id,name',
                'block:id,name',
                'creator:id,name',
                'completionSubmitter:id,name',
            ])
            ->where(function ($builder) use ($userId) {
                $builder->where('pic_user_id', $userId)
                    ->orWhereJsonContains('pic_user_ids', $userId);
            })
            ->whereIn('status', [QcFinding::STATUS_OPEN, QcFinding::STATUS_IN_REVIEW])
            ->where(function ($builder) {
                $builder->whereNull('completion_submitted_at')
                    ->orWhere('needs_resubmission', true);
            });
    }

    private function canApproveStep(QcFinding $finding, QcFindingApprovalStep $step, int $userId): bool
    {
        $isCurrentLevel = (int) ($finding->currentPendingApprovalStep()?->id ?? 0) === (int) $step->id;

        if (!$isCurrentLevel) {
            return false;
        }

        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // QC Admin may approve at any level regardless of assignment
        if ($user->hasModuleRole('qc', 'QC Admin')) {
            return true;
        }

        // QC Approver may only approve the step they are explicitly assigned to
        if ($user->hasModuleRole('qc', 'QC Approver')) {
            return (int) $step->approver_user_id === $userId;
        }

        return false;
    }
}
