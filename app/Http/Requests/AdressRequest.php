<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdressRequest extends FormRequest
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
            'name'=>'required|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required|string|max:255',
            'lang' => 'required|string',
            'lat' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'default' => 'nullable|boolean',
        ];
    }
}
