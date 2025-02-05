<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'medico_id' => 'required|integer|exists:medicos,id',
            'paciente_id' => 'required|integer|exists:pacientes,id',
            'data' => 'required|date|date_format:Y-m-d H:i:s|after:today',
        ];
    }
}
