<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexQuery extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $array = collect(config('options'))->map(function($value){
            return ucfirst($value);
        });
        return [
            'area' => ['nullable', 'string', Rule::in($array)],
            'keyword' => 'nullable|string'
        ];
    }
}
