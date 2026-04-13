<?php

namespace Modules\QcComplaintSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetQcFindingDeadlineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_resolution_date' => 'required|date|after_or_equal:today',
            'follow_up_plan' => 'nullable|string|max:2000',
        ];
    }
}
