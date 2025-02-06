<?php

namespace App\Http\Requests\SickGuard\Profil\Qualification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SickGuardDeleteQualificationRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation à appliquer à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:qualifications,id'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Le champ id est obligatoire' ,
            'id.exists' => 'La qualification n\'existe pas'
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
