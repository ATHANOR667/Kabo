<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ClientSigninRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Récupère les règles de validation pour la requête.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'prohibited',
            'matricule' => 'prohibited',
            'nom' => 'required|string|max:25',
            'prenom' => 'required|string|max:25',
            'telephone' => 'required|string|max:20',
           /* 'pays' => 'required|string|max:25',
            'ville' => 'required|string|max:25',
            'quartier' => 'required|string|max:25',*/
            'dateNaissance' => 'required|date|before_or_equal:2010-01-01',
            'lieuNaissance' => 'required|string|max:50',
            'photoProfil' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pieceIdentite' => 'nullable|mimes:pdf,jpeg,png|max:2048',
        ];
    }

    /**
     * Récupère les messages d'erreur personnalisés.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'password.prohibited' => 'Vous ne pouvez pas définir de mot de passe.',
            'matricule.prohibited' => 'Vous ne pouvez pas définir le matricule.',

            'nom.required' => 'Le champ nom est obligatoire. Veuillez fournir votre nom complet.',
            'nom.string' => 'Le nom doit être une chaîne de caractères valide.',
            'nom.max' => 'Le nom ne peut pas dépasser 25 caractères.',

            'prenom.required' => 'Le champ prénom est obligatoire. Veuillez fournir votre prénom.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères valide.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 25 caractères.',

            'telephone.required' => 'Le numéro de téléphone est obligatoire. Veuillez fournir un numéro valide.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',

            'pays.required' => 'Le pays est obligatoire. Veuillez fournir votre pays.',
            'pays.string' => 'Le pays doit être une chaîne de caractères valide.',
            'pays.max' => 'Le pays ne peut pas dépasser 25 caractères.',

            'ville.required' => 'La ville est obligatoire. Veuillez fournir la ville.',
            'ville.string' => 'La ville doit être une chaîne de caractères valide.',
            'ville.max' => 'La ville ne peut pas dépasser 25 caractères.',

            'quartier.required' => 'Le quartier est obligatoire. Veuillez fournir votre quartier.',
            'quartier.string' => 'Le quartier doit être une chaîne de caractères valide.',
            'quartier.max' => 'Le quartier ne peut pas dépasser 25 caractères.',

            'photoProfil.image' => 'Le fichier photo de profil doit être une image valide.',
            'photoProfil.mimes' => 'Le fichier photo de profil doit être une image de type jpeg, png, jpg, gif ou svg.',
            'photoProfil.max' => 'Le fichier photo de profil ne doit pas dépasser 2 Mo.',

            'pieceIdentite.mimes' => 'Le fichier de la pièce d\'identité doit être au format PDF, jpeg ou png.',
            'pieceIdentite.max' => 'Le fichier de la pièce d\'identité ne doit pas dépasser 2 Mo.',

            'dateNaissance.required' => 'La date de naissance est obligatoire. Veuillez fournir votre date de naissance.',
            'dateNaissance.date' => 'La date de naissance doit être une date valide.',
            'dateNaissance.before_or_equal' => 'La date de naissance doit être avant le 1er janvier 2010.',

            'lieuNaissance.required' => 'Le lieu de naissance est obligatoire. Veuillez fournir le lieu de naissance.',
            'lieuNaissance.string' => 'Le lieu de naissance doit être une chaîne de caractères valide.',
            'lieuNaissance.max' => 'Le lieu de naissance ne peut pas dépasser 50 caractères.',

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
