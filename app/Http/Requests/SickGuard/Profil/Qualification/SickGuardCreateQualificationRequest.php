<?php

namespace App\Http\Requests\SickGuard\Profil\Qualification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SickGuardCreateQualificationRequest extends FormRequest
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
            'titre' => 'required|string|max:255',
            'annee' => 'required|integer|min:1900|max:'.date('Y'),
            'mention' => 'nullable|string|max:255',
            'institutionReference' => 'required|string|max:255',
            'fichier' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
            'sick_guard_id' => 'required|exists:sick_guards,id',
        ];
    }

    /**
     * Messages d'erreur personnalisés pour la validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'annee.required' => 'L\'année est obligatoire.',
            'annee.integer' => 'L\'année doit être un entier.',
            'annee.min' => 'L\'année ne peut pas être inférieure à 1900.',
            'annee.max' => 'L\'année ne peut pas être supérieure à l\'année en cours.',

            'mention.string' => 'La mention doit être une chaîne de caractères.',
            'mention.max' => 'La mention ne doit pas dépasser 255 caractères.',

            'institutionReference.required' => 'La référence de l\'institution est obligatoire.',
            'institutionReference.string' => 'La référence de l\'institution doit être une chaîne de caractères.',
            'institutionReference.max' => 'La référence de l\'institution ne doit pas dépasser 255 caractères.',

            'fichier.file' => 'Le fichier doit être un fichier valide.',
            'fichier.mimes' => 'Le fichier doit être au format PDF, JPG, PNG, DOC, ou DOCX.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 10 Mo.',

            'sick_guard_id.required' => 'L\'ID du sick_guard est obligatoire.',
            'sick_guard_id.exists' => 'L\'ID du sick_guard spécifié n\'existe pas.',
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
