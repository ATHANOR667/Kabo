<?php

namespace App\Http\Requests\SuperAdmin\GestionAdmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SuperadminEditAdminRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return Auth::guard('superadmin')->check();
    }

    /**
     * Récupère les règles de validation pour la requête.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:admins,id',
            'password' => 'prohibited',
            'email' => 'prohibited',
            'matricule' => 'prohibited',
            'nom' => 'nullable|string|min:3|max:25',
            'prenom' => 'nullable|string|min:3|max:25',
            'telephone' => 'nullable|string|max:20',
            'pays' => 'nullable|string|min:3|max:25',
            'ville' => 'nullable|string|min:3|max:25',
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
            'id.required' => 'le champ id requis',
            'id.exists' => 'id renseigné ne correspond a aucun admin ',

            'password.prohibited' => 'vous ne pouvez pas éditer mot de passe',
            'email.prohibited' => 'vous ne pouvez pas éditer l\'adresse email',
            'matricule.prohibited' => 'vous ne pouvez pas éditer le matricule',

            'nom.nullable' => 'Le champ nom  doit être une chaîne de caractères d\'au moins 3 caractères et au maximum 25.',
            'nom.string' => 'Le champ nom doit être une chaîne de caractères.',
            'nom.min' => 'Le champ nom doit contenir au moins 3 caractères.',
            'nom.max' => 'Le champ nom ne peut pas dépasser 25 caractères.',

            'prenom.nullable' => 'Le champ prénom  doit être une chaîne de caractères d\'au moins 3 caractères et au maximum 25.',
            'prenom.string' => 'Le champ prénom doit être une chaîne de caractères.',
            'prenom.min' => 'Le champ prénom doit contenir au moins 3 caractères.',
            'prenom.max' => 'Le champ prénom ne peut pas dépasser 25 caractères.',

            'telephone.nullable' => 'Le numéro de téléphone est optionnel.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',

            'pays.nullable' => 'Le pays  doit être une chaîne de caractères d\'au moins 3 caractères et au maximum 25.',
            'pays.string' => 'Le pays doit être une chaîne de caractères.',
            'pays.min' => 'Le pays doit contenir au moins 3 caractères.',
            'pays.max' => 'Le pays ne peut pas dépasser 25 caractères.',

            'ville.nullable' => 'La ville doit être une chaîne de caractères d\'au moins 3 caractères et au maximum 25.',
            'ville.string' => 'La ville doit être une chaîne de caractères.',
            'ville.min' => 'La ville doit contenir au moins 3 caractères.',
            'ville.max' => 'La ville ne peut pas dépasser 25 caractères.',

            'photoProfil.nullable' => 'Le fichier photo de profil est optionnel.',
            'photoProfil.image' => 'Le fichier photo de profil doit être une image.',
            'photoProfil.mimes' => 'Le fichier photo de profil doit être de type jpeg, png, jpg, gif, ou svg.',
            'photoProfil.max' => 'Le fichier photo de profil ne doit pas dépasser 2 Mo.',

            'pieceIdentite.nullable' => 'Le fichier de la pièce d\'identité est optionnel.',
            'pieceIdentite.mimes' => 'Le fichier de la pièce d\'identité doit être un fichier PDF, jpeg, ou png.',
            'pieceIdentite.max' => 'Le fichier de la pièce d\'identité ne doit pas dépasser 2 Mo.',
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
