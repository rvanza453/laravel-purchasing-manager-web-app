<?php

namespace Modules\ServiceAgreementSystem\Services;

use Illuminate\Support\Facades\Log;
use Modules\ServiceAgreementSystem\Models\UspkApprovalSchemaStep;
use Modules\ServiceAgreementSystem\Models\UspkApproval;
use Modules\ServiceAgreementSystem\Models\UspkTender;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class UspkApprovalService
{
    /**
     * Approve USPK
     */
    public function approve(UspkSubmission $submission, int $userId, ?string $comment = null, ?int $selectedTenderId = null, array $voteData = []): UspkApproval
    {
        $user = \App\Models\User::find($userId);
        $isSuperAdmin = $user->hasRole('Super Admin');

        return $this->applyDecision(
            submission: $submission,
            userId: $userId,
            decisionStatus: UspkApproval::STATUS_APPROVED,
            comment: $comment,
            selectedTenderId: $selectedTenderId,
            voteData: $voteData,
            isSuperAdmin: $isSuperAdmin
        );
    }

    public function hold(UspkSubmission $submission, int $userId, ?string $comment = null, ?int $selectedTenderId = null, array $voteData = []): UspkApproval
    {
        $user = \App\Models\User::find($userId);
        $isSuperAdmin = $user->hasRole('Super Admin');

        return $this->applyDecision(
            submission: $submission,
            userId: $userId,
            decisionStatus: UspkApproval::STATUS_ON_HOLD,
            comment: $comment,
            selectedTenderId: $selectedTenderId,
            voteData: $voteData,
            isSuperAdmin: $isSuperAdmin
        );
    }

    /**
     * Reject USPK
     */
    public function reject(UspkSubmission $submission, int $userId, ?string $comment = null, array $voteData = []): UspkApproval
    {
        $user = \App\Models\User::find($userId);
        $isSuperAdmin = $user->hasRole('Super Admin');

        return $this->applyDecision(
            submission: $submission,
            userId: $userId,
            decisionStatus: UspkApproval::STATUS_REJECTED,
            comment: $comment,
            selectedTenderId: $voteData['vote_tender_id'] ?? null,
            voteData: $voteData,
            isSuperAdmin: $isSuperAdmin
        );
    }

    protected function applyDecision(
        UspkSubmission $submission,
        int $userId,
        string $decisionStatus,
        ?string $comment,
        ?int $selectedTenderId,
        array $voteData,
        bool $isSuperAdmin
    ): UspkApproval {
        $approval = $submission->approvals()
            ->whereIn('status', [UspkApproval::STATUS_PENDING, UspkApproval::STATUS_ON_HOLD])
            ->orderBy('level', 'asc')
            ->first();

        if (!$approval) {
            throw new \Exception('Sudah tidak ada tahap yang perlu diproses.');
        }

        $schema = $approval->schema ?: $submission->department?->approvalSchemas()
            ->where('is_active', true)
            ->with('steps')
            ->first();

        if (!$schema) {
            throw new \Exception('Skema approval untuk USPK ini tidak ditemukan.');
        }

        $step = UspkApprovalSchemaStep::where('schema_id', $schema->id)
            ->where('level', $approval->level)
            ->first();

        // Super Admin bisa override siapa saja
        if (!$isSuperAdmin && (!$step || $step->user_id !== $userId)) {
            throw new \Exception('Anda tidak memiliki otorisasi untuk melakukan proses pada tahap ini.');
        }

        $this->storeVoteData($approval, $voteData);

        $approval->update([
            'status' => $decisionStatus,
            'user_id' => $userId,
            'comment' => $comment,
            'approved_at' => now(),
        ]);

        if (in_array($decisionStatus, [UspkApproval::STATUS_APPROVED, UspkApproval::STATUS_ON_HOLD], true) && $selectedTenderId) {
            $this->persistTenderNegotiation($submission, $selectedTenderId, $voteData, $userId, $approval->level);
        }

        if ($decisionStatus === UspkApproval::STATUS_REJECTED) {
            $submission->update(['status' => UspkSubmission::STATUS_REJECTED]);
        } elseif ($decisionStatus === UspkApproval::STATUS_ON_HOLD) {
            $submission->update(['status' => UspkSubmission::STATUS_IN_REVIEW]);
        } else {
            $isFinalLevel = (int) $approval->level === (int) $schema->steps->max('level');

            if ($isFinalLevel) {
                $chosenTenderId = $selectedTenderId ?: $submission->tenders()->where('is_selected', true)->value('id');

                if ($chosenTenderId) {
                    $this->applyTenderVote($submission, $chosenTenderId, $voteData);
                }

                $submission->update(['status' => UspkSubmission::STATUS_APPROVED]);
            } else {
                $submission->update(['status' => UspkSubmission::STATUS_IN_REVIEW]);

                if ($selectedTenderId) {
                    Log::info('Tender vote recorded during USPK approval', [
                        'uspk_id' => $submission->id,
                        'tender_id' => $selectedTenderId,
                        'approver_id' => $userId,
                        'level' => $approval->level,
                    ]);
                }
            }
        }

        Log::info('USPK approval action stored', [
            'uspk_id' => $submission->id,
            'approver_id' => $userId,
            'level' => $approval->level,
            'status' => $decisionStatus,
        ]);

        return $approval;
    }

    protected function storeVoteData(UspkApproval $approval, array $voteData): void
    {
        $approval->fill([
            'vote_tender_id' => $voteData['vote_tender_id'] ?? null,
            'vote_tender_value' => $voteData['vote_tender_value'] ?? null,
            'vote_tender_duration' => $voteData['vote_tender_duration'] ?? null,
            'vote_tender_description' => $voteData['vote_tender_description'] ?? null,
        ]);

        $approval->save();
    }

    protected function applyTenderVote(UspkSubmission $submission, int $tenderId, array $voteData): void
    {
        $tender = $submission->tenders()->where('id', $tenderId)->first();

        if (!$tender) {
            throw new \Exception('Tender yang dipilih tidak ditemukan pada USPK ini.');
        }

        $submission->tenders()->update(['is_selected' => false]);

        $updates = ['is_selected' => true];

        if (array_key_exists('vote_tender_value', $voteData) && $voteData['vote_tender_value'] !== null && $voteData['vote_tender_value'] !== '') {
            $updates['tender_value'] = $voteData['vote_tender_value'];
        }

        if (array_key_exists('vote_tender_duration', $voteData) && $voteData['vote_tender_duration'] !== null && $voteData['vote_tender_duration'] !== '') {
            $updates['tender_duration'] = $voteData['vote_tender_duration'];
        }

        if (array_key_exists('vote_tender_description', $voteData) && $voteData['vote_tender_description'] !== null) {
            $updates['description'] = $voteData['vote_tender_description'];
        }

        $tender->update($updates);

        Log::info('Final contractor selection applied', [
            'uspk_id' => $submission->id,
            'tender_id' => $tenderId,
            'updates' => $updates,
        ]);
    }

    protected function persistTenderNegotiation(UspkSubmission $submission, int $tenderId, array $voteData, int $userId, int $approvalLevel): void
    {
        $tender = $submission->tenders()->where('id', $tenderId)->first();

        if (!$tender) {
            throw new \Exception('Tender yang dipilih tidak ditemukan pada USPK ini.');
        }

        $updates = [];

        if (array_key_exists('vote_tender_value', $voteData) && $voteData['vote_tender_value'] !== null && $voteData['vote_tender_value'] !== '') {
            $updates['tender_value'] = $voteData['vote_tender_value'];
        }

        if (array_key_exists('vote_tender_duration', $voteData) && $voteData['vote_tender_duration'] !== null && $voteData['vote_tender_duration'] !== '') {
            $updates['tender_duration'] = $voteData['vote_tender_duration'];
        }

        if (array_key_exists('vote_tender_description', $voteData) && $voteData['vote_tender_description'] !== null) {
            $updates['description'] = $voteData['vote_tender_description'];
        }

        if (empty($updates)) {
            return;
        }

        $tender->update($updates);

        Log::info('Tender negotiation values persisted', [
            'uspk_id' => $submission->id,
            'tender_id' => $tenderId,
            'approver_id' => $userId,
            'level' => $approvalLevel,
            'updates' => $updates,
        ]);
    }
}
