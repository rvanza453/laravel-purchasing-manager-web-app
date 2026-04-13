<?php

namespace Modules\QcComplaintSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\QcComplaintSystem\Http\Requests\UpdateQcApprovalConfigRequest;
use Modules\QcComplaintSystem\Services\QcApprovalConfigService;
use Modules\ServiceAgreementSystem\Models\Department;

class QcApprovalConfigController extends Controller
{
    public function __construct(
        protected QcApprovalConfigService $configService
    ) {}

    public function index()
    {
        $departments = Department::query()->orderBy('name')->get(['id', 'name']);

        return view('qccomplaintsystem::approval-config.index', [
            'departments' => $departments,
        ]);
    }

    public function edit(Department $department)
    {
        return view('qccomplaintsystem::approval-config.edit', [
            'department' => $department,
            'config' => $this->configService->getConfigForDepartment($department->id),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateQcApprovalConfigRequest $request, Department $department)
    {
        $payload = $request->validated();

        $this->configService->updateApproversForDepartment(
            $department->id,
            $request->validated()['approver_user_ids'],
            (int) auth()->id()
        );

        return redirect()->route('qc.approval-config.edit', $department)
            ->with('success', 'Konfigurasi approver QC berhasil diperbarui.');
    }
}
