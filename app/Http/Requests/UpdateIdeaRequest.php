<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'based_on_previous' => 'nullable|in:yes,no',
            'parent_id'         => 'nullable|integer|exists:ideas,id',
            'title'             => 'required|string|max:120',
            'summary'           => 'required|string|max:300',
            'problem'           => 'required|string|min:20',
            'solution'          => 'required|string|min:20',
            'category'          => 'required|string|max:80',
            'budget'            => 'nullable|string|max:120',
            'technologies'      => 'nullable|array',
            'technologies.*'    => 'string|max:255',
            'feasibility'       => 'nullable|string|max:5000',
            'ip_agreement'      => 'nullable',
            'intent'            => 'nullable|in:draft,pending',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('technologies')) {
            $this->merge([
                'technologies' => array_values(array_filter(
                    (array) $this->technologies,
                    fn ($v) => is_string($v) && trim($v) !== ''
                )),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'problem.min'      => 'وضّح المشكلة بوضوح (20 حرفاً على الأقل).',
            'solution.min'     => 'وضّح الحل بوضوح (20 حرفاً على الأقل).',
            'summary.required' => 'الوصف المختصر مطلوب ضمن معايير القبول.',
        ];
    }
}
