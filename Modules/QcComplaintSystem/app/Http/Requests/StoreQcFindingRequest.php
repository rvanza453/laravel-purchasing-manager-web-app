<?php

namespace Modules\QcComplaintSystem\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Modules\QcComplaintSystem\Models\QcFinding;

class StoreQcFindingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

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

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException('Hanya staff dari HO dan QC yang bisa menambahkan temuan.');
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('finding_items')) {
            return;
        }

        $items = array_values(array_filter((array) $this->input('finding_items', []), function ($item) {
            if (!is_array($item)) {
                return false;
            }

            foreach (['template_key', 'label', 'quantity', 'note'] as $key) {
                $value = $item[$key] ?? null;

                if (is_string($value)) {
                    $value = trim($value);
                }

                if ($value !== null && $value !== '') {
                    return true;
                }
            }

            return false;
        }));

        $this->merge([
            'finding_items' => $items,
        ]);
    }

    public function rules(): array
    {
        $sourceOptions = array_merge(QcFinding::sourceOptions(), ['other']);

        return [
            'finding_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'inspection_context' => 'nullable|array|required_if:kategori,panen',
            'inspection_context.total_ha_block' => 'nullable|numeric|min:0',
            'inspection_context.sph' => 'nullable|numeric|min:0',
            'inspection_context.abw' => 'nullable|numeric|min:0',
            'inspection_context.alw' => 'nullable|numeric|min:0',
            'inspection_context.inspection_date' => 'nullable|date',
            'inspection_context.inspector_name' => 'nullable|string|max:255',
            'inspection_context.assistant_witness' => 'nullable|string|max:255',
            'inspection_context.mandor_witness' => 'nullable|string|max:255',
            'finding_items' => 'nullable|array|min:1|required_if:kategori,panen',
            'finding_items.*.template_key' => 'nullable|string|max:100',
            'finding_items.*.label' => 'required_with:finding_items|string|max:255',
            'finding_items.*.quantity' => 'nullable|numeric|min:0',
            'finding_items.*.note' => 'nullable|string|max:500',
            'finding_attachments' => 'nullable|array|max:10',
            'finding_attachments.*' => 'file|max:20480',
            'finding_attachments_remove' => 'nullable|array',
            'finding_attachments_remove.*' => 'nullable|string',
            'source_type' => 'required|in:' . implode(',', $sourceOptions),
            'source_type_custom' => 'nullable|required_if:source_type,other|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'sub_department_id' => 'required|exists:sub_departments,id',
            'block_name' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'urgency' => 'required|in:' . implode(',', QcFinding::urgencyOptions()),
            'pic_user_id' => 'nullable|exists:users,id',
            'pic_user_ids' => 'nullable|array',
            'pic_user_ids.*' => 'nullable|exists:users,id',
            'kategori' => 'nullable|in:' . implode(',', QcFinding::categoryOptions()),
            'sub_kategori' => 'nullable|string|max:100',
        ];
    }
}
