<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportCsvRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        return $this->query->all();
    }


    public function rules(): array
    {
        return [
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'delimiter'    => ['nullable', 'string', Rule::in([';', ','])],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.date'        => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.date'           => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'delimiter.in'             => 'El delimitador debe ser ";" o ",".',
        ];
    }
}
