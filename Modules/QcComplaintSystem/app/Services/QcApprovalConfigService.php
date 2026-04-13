<?php

namespace Modules\QcComplaintSystem\Services;

use Modules\QcComplaintSystem\Models\QcApprovalConfig;

class QcApprovalConfigService
{
    public function getActiveConfig(): ?QcApprovalConfig
    {
        return QcApprovalConfig::query()->latest('id')->first();
    }

    public function getConfigForDepartment(int $departmentId): ?QcApprovalConfig
    {
        return QcApprovalConfig::query()
            ->where('department_id', $departmentId)
            ->latest('id')
            ->first();
    }

    public function getApproverIdsForDepartment(int $departmentId): array
    {
        $config = $this->getConfigForDepartment($departmentId);

        // Backward-compatible fallback for older global config rows without department_id.
        if (!$config) {
            $config = QcApprovalConfig::query()
                ->whereNull('department_id')
                ->latest('id')
                ->first();
        }

        return array_values(array_unique(array_map('intval', $config?->approver_user_ids ?? [])));
    }

    public function updateApprovers(array $approverUserIds, int $updatedBy): QcApprovalConfig
    {
        return $this->updateApproversForDepartment(0, $approverUserIds, $updatedBy);
    }

    public function updateApproversForDepartment(int $departmentId, array $approverUserIds, int $updatedBy): QcApprovalConfig
    {
        $approverUserIds = array_values(array_unique(array_map('intval', $approverUserIds)));

        $config = $departmentId > 0
            ? $this->getConfigForDepartment($departmentId)
            : $this->getActiveConfig();

        if (!$config) {
            return QcApprovalConfig::create([
                'department_id' => $departmentId > 0 ? $departmentId : null,
                'approver_user_id' => $approverUserIds[0],
                'approver_user_ids' => $approverUserIds,
                'updated_by' => $updatedBy,
            ]);
        }

        $config->update([
            'department_id' => $departmentId > 0 ? $departmentId : $config->department_id,
            'approver_user_id' => $approverUserIds[0],
            'approver_user_ids' => $approverUserIds,
            'updated_by' => $updatedBy,
        ]);

        return $config->fresh();
    }

    public function canApprove(int $userId): bool
    {
        return QcApprovalConfig::query()
            ->get()
            ->contains(function (QcApprovalConfig $config) use ($userId) {
                return in_array($userId, $config->approver_user_ids, true)
                    || (int) $config->approver_user_id === $userId;
            });
    }
}
