<?php

namespace App\Http\Requests\SickGuard\WorkFlow\Disponibilite;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SickGuardDeleteDisponibiliteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('sickguard')->check();
    }

    /**
     * Récupère les règles de validation pour la requête.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'debut' => [
                'required',
                'integer',
                'min:0',
                'max:23',
                function ($attribute, $value, $fail) {
                    if ($value < now()->hour) {
                        $fail("L'heure de début ne peut pas être dans le passé.");
                    }
                },
            ],
            'date' => 'required|date_format:d-m-Y|after_or_equal:today',
        ] ;
    }

    /**
     * Récupère les messages d'erreur personnalisés.
     *
     * @return array
     */
    public function messages(): array
    {
        return  [
            'debut.required' => "L'heure de début est requise.",
            'debut.integer' => "L'heure de début doit être un nombre entier.",
            'debut.min' => "L'heure de début ne peut pas être inférieure à 0.",
            'debut.max' => "L'heure de début ne peut pas être supérieure à 23.",
            'date.required' => "La date est requise.",
            'date.date_format' => "Le format de la date doit être jj-mm-aaaa.",
            'date.after_or_equal' => "La date doit être aujourd'hui ou une date future.",
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
