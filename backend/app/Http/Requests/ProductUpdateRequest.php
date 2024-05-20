<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'category_id' => 'required|integer|exists:categories,id'
        ];
    }
}
