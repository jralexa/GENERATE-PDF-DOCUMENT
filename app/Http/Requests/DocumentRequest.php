<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
            'document_no' => ['required', 'string', 'max:20'],
            'document_year' => ['required', 'digits:4'],
            'document_date' => ['required', 'date'],
            'employee_name' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:150'],
            'assignment_station' => ['required', 'string', 'max:200'],
            'conforme_name' => ['required', 'string', 'max:150'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_no.required' => 'Document number is required.',
            'document_no.max' => 'Document number may not be greater than 20 characters.',
            'document_year.required' => 'Document year is required.',
            'document_year.digits' => 'Document year must be exactly 4 digits.',
            'document_date.required' => 'Document date is required.',
            'document_date.date' => 'Document date must be a valid date.',
            'employee_name.required' => 'Employee name is required.',
            'employee_name.max' => 'Employee name may not be greater than 150 characters.',
            'position.required' => 'Position is required.',
            'position.max' => 'Position may not be greater than 150 characters.',
            'assignment_station.required' => 'Assignment station is required.',
            'assignment_station.max' => 'Assignment station may not be greater than 200 characters.',
            'conforme_name.required' => 'Conforme name is required.',
            'conforme_name.max' => 'Conforme name may not be greater than 150 characters.',
        ];
    }
}
