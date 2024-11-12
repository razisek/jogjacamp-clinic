<?php

namespace App\Http\Requests\Diagnose;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnoseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:diagnoses,name'],
        ];
    }
}
