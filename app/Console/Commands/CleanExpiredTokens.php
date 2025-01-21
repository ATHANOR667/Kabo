<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class CleanExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime les tokens qui ont expiré';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        PersonalAccessToken::where('expires_at', '<', Carbon::now())->delete();

        $this->info('Tokens expirés  supprimés avec succès.');
    }
}
