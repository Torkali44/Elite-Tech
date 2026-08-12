<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KycSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'doc_type' => 'required|string|in:national_id,passport,driver_license',
            'purpose'  => 'required|string|in:publish_idea,implement,jobs_forum',
            'id_front' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_back'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'selfie'   => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'doc_type.required' => $isAr ? 'اختر نوع المستند.' : 'Select document type.',
            'id_front.required' => $isAr ? 'يرجى رفع صورة الجهة الأمامية للمستند.' : 'Please upload front side of the document.',
        ];
    }
}
