<?php

namespace App\Http\Requests\SickGuard\Profil\Experience;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SickGuardCreateExperienceRequest extends FormRequest
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
            'nomEntreprise' => 'required|string|max:255',
            'typeEntreprise' => 'required|string|max:255',
            'nomReferent' => 'string|max:255',
            'numeroReferent' => 'numeric',
            'posteReferent' => 'string|max:255',
            'dateDebut' => 'required|date_format:Y-m-d',
            'dateFin' => 'nullable|date_format:Y-m-d|after_or_equal:dateDebut',
            'poste' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
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
            'nomEntreprise.required' => 'Le nom de l\'entreprise est obligatoire.',
            'nomEntreprise.string' => 'Le nom de l\'entreprise doit être une chaîne de caractères valide.',
            'nomEntreprise.max' => 'Le nom de l\'entreprise ne peut pas dépasser 255 caractères.',


            'typeEntreprise.required' => 'Le type d\'entreprise est obligatoire.',
            'typeEntreprise.string' => 'Le type de l\'entreprise doit être une chaîne de caractères valide.',
            'typeEntreprise.max' => 'Le type de l\'entreprise ne peut pas dépasser 255 caractères.',


            'nomReferent.required' => 'Le nom du référent est obligatoire.',
            'nomReferent.string' => 'Le nom du referent doit être une chaîne de caractères valide.',
            'nomReferent.max' => 'Le nom du referent ne peut pas dépasser 255 caractères.',


            'numeroReferent.required' => 'Le numéro de téléphone du référent est obligatoire.',
            'numeroReferent.numeric' => 'Le numéro de téléphone du référent doit être un nombre.',

            'posteReferent.required' => 'Le poste du référent est obligatoire.',
            'posteReferent.string' => 'Le nom du réferent doit être une chaîne de caractères valide.',
            'posteReferent.max' => 'Le nom du réferent ne peut pas dépasser 255 caractères.',


            'dateDebut.required' => 'La date de début est obligatoire.',
            'dateDebut.date_format' => 'Le format de la date de début doit être YYYY-MM-DD.',

            'dateFin.date_format' => 'Le format de la date de fin doit être YYYY-MM-DD.',
            'dateFin.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',

            'poste.required' => 'Le poste est obligatoire.',
            'poste.string' => 'Le poste doit être une chaîne de caractères valide.',
            'poste.max' => 'Le poste ne peut pas dépasser 255 caractères.',


            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description  ne peut pas dépasser 255 caractères.',


            'sick_guard_id.required' => 'L\'ID du SickGuard est obligatoire.',
            'sick_guard_id.exists' => 'L\'ID du SickGuard fourni n\'est pas valide.',
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
