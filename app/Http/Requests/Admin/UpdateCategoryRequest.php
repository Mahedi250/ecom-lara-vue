<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'staff']);
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'parent_id'        => ['nullable', 'exists:categories,id'],
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'description'      => ['nullable', 'string'],
            'image'            => ['nullable', 'image', 'max:2048'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords'    => ['nullable', 'string', 'max:255'],
            'is_active'        => ['boolean'],
            'sort_order'       => ['integer', 'min:0'],
        ];
    }
}
