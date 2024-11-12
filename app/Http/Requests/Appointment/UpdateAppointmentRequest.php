<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['sometimes', 'exists:patients,id'],
            'diagnose_id' => ['sometimes', 'exists:diagnoses,id'],
            'status' => ['required', 'boolean'],
        ];
    }
}
