<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    $categoryId = $this->route('category')?->id ?? $this->route('id');
    return [
        'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
        'slug' => 'required|string|max:255|unique:categories,slug,' . $categoryId,
    ];
}


}
