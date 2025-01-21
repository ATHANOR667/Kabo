<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SickGuard;
use App\Models\Admin;
use App\Models\Client;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DeleteUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:images:delete-unused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime les images stockées mais non utilisées dans les champs photoProfil et pieceIdentite des modèles SickGuard, Admin, Client et SuperAdmin.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exécute la commande.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $usedPhotoProfiles = collect();
            $usedPieceIdentites = collect();

            $usedPhotoProfiles = $usedPhotoProfiles->merge(SickGuard::whereNotNull('photoProfil')->get());
            $usedPieceIdentites = $usedPieceIdentites->merge(SickGuard::whereNotNull('pieceIdentite')->get());

            $usedPhotoProfiles = $usedPhotoProfiles->merge(Admin::whereNotNull('photoProfil')->get());
            $usedPieceIdentites = $usedPieceIdentites->merge(Admin::whereNotNull('pieceIdentite')->get());

            $usedPhotoProfiles = $usedPhotoProfiles->merge(Client::whereNotNull('photoProfil')->get());
            $usedPieceIdentites = $usedPieceIdentites->merge(Client::whereNotNull('pieceIdentite')->get());

         /*   $usedPhotoProfiles = $usedPhotoProfiles->merge(SuperAdmin::whereNotNull('photoProfil')->get());
            $usedPieceIdentites = $usedPieceIdentites->merge(SuperAdmin::whereNotNull('pieceIdentite')->get());*/

            $profileFiles = Storage::disk('public')->files('profiles');
            $identityFiles = Storage::disk('public')->files('identities');

            $usedPhotoFileNames = [];
            foreach ($usedPhotoProfiles as $user) {
                if ($user->photoProfil) {
                    $usedPhotoFileNames[] = basename($user->photoProfil);
                }
            }

            $usedIdentityFileNames = [];
            foreach ($usedPieceIdentites as $user) {
                if ($user->pieceIdentite) {
                    $usedIdentityFileNames[] = basename($user->pieceIdentite);
                }
            }

            Log::info('Fichiers dans public/profiles :', $profileFiles);
            Log::info('Fichiers dans public/identities :', $identityFiles);
            Log::info('Fichiers utilisés pour photoProfil :', $usedPhotoFileNames);
            Log::info('Fichiers utilisés pour pieceIdentite :', $usedIdentityFileNames);

            foreach ($profileFiles as $file) {
                $fileName = basename($file);

                if (!in_array($fileName, $usedPhotoFileNames)) {
                    Storage::disk('public')->delete('profiles/' . $fileName);
                    Log::info("Fichier supprimé (profile): " . $fileName);
                }
            }

            foreach ($identityFiles as $file) {
                $fileName = basename($file);

                if (!in_array($fileName, $usedIdentityFileNames)) {
                    Storage::disk('public')->delete('identities/' . $fileName);
                    Log::info("Fichier supprimé (identity): " . $fileName);
                }
            }

            $this->info('Nettoyage des images inutilisées terminé.');
        } catch (\Exception $e) {
            Log::error('Erreur lors du nettoyage des images : ' . $e->getMessage());
            $this->error('Une erreur est survenue lors de la suppression des images inutilisées.');
        }
    }
}
