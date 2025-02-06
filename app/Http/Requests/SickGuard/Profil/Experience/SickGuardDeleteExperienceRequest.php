<?php

namespace App\Http\Requests\SickGuard\Profil\Experience;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SickGuardDeleteExperienceRequest extends FormRequest
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
            'id' => 'required|exists:experiences,id'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Le champ id est obligatoire' ,
            'id.exists' => 'L\'experience n\'existe pas'
        ];
    }

    /**
     * Gérer l'échec de la validation.
     * @throws ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'status' => 422,
            'message' => 'Échec de la validation des données.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
