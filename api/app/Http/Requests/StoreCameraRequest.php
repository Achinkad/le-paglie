<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCameraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'ip_address' => ['required', 'ip', Rule::unique('cameras')->ignore($this->camera)],
            'location' => 'required',
            'authorization' => 'required'
        ];
    }
}
