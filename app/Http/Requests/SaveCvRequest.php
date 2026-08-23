<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'                          => 'nullable|string|max:120',
            'summary'                        => 'nullable|string|max:2000',
            'skills'                         => 'nullable|string|max:1000',
            'portfolio_url'                  => 'nullable|url|max:255',
            'phone'                          => 'nullable|string|max:40',
            'location'                       => 'nullable|string|max:120',
            'linkedin'                       => 'nullable|url|max:255',
            'github'                         => 'nullable|url|max:255',
            'languages'                      => 'nullable|string|max:500',
            'certifications'                 => 'nullable|string|max:1000',
            'years_experience'               => 'nullable|string|max:20',
            'availability'                   => 'nullable|string|max:80',
            'expected_salary'                => 'nullable|string|max:80',
            'theme_color'                    => 'nullable|string|max:50',
            'theme_font'                     => 'nullable|string|max:100',
            'join_forum'                     => 'nullable|boolean',
            'avatar'                         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'experience_items'               => 'nullable|array|max:25',
            'experience_items.*.title'       => 'nullable|string|max:200',
            'experience_items.*.company'     => 'nullable|string|max:200',
            'experience_items.*.dates'       => 'nullable|string|max:80',
            'experience_items.*.description' => 'nullable|string|max:3000',
            'project_items'                  => 'nullable|array|max:25',
            'project_items.*.title'          => 'nullable|string|max:200',
            'project_items.*.dates'          => 'nullable|string|max:80',
            'project_items.*.description'    => 'nullable|string|max:3000',
            'project_items.*.url'            => 'nullable|url|max:255',
            'education_items'                => 'nullable|array|max:15',
            'education_items.*.title'        => 'nullable|string|max:200',
            'education_items.*.institution'  => 'nullable|string|max:200',
            'education_items.*.dates'        => 'nullable|string|max:80',
            'education_items.*.description'  => 'nullable|string|max:1000',
        ];
    }
}
