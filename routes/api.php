<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/**
 *
 *
 *
 *
 *
 *ROUTES LIEES A L'AUTHENTIFICATION DU SUPERADMIN
 *
 *
 *
 *
 *
 */


/**
 * ROUTES HORS CONNEXION
 */

Route::controller(\App\Http\Controllers\SuperAdmin\SuperAdminAuthController::class)
    ->name('superAdmin.')
    ->prefix('superadmin')
    ->group(function (){

        Route::post('/login', 'login')->name('login');

        Route::get('/default', 'default')->name('default');

        Route::post('/password-reset-while-dissconnected-init', 'password_reset_while_dissconnected_init')->name('password_reset_while_dissconnected_init');
        Route::patch('/password-reset-while-dissconnected-process', 'password_reset_while_dissconnected_process')->name('password_reset_while_dissconnected_process');



    });

/**
 *ROUTES AVEC CONNEXION
 */
Route::controller(\App\Http\Controllers\SuperAdmin\SuperAdminAuthController::class)
    ->name('superAdmin.')
    ->middleware('superadmin')
    ->prefix('superadmin')
    ->group(function ()
    {

        Route::post('/otp-request', 'otp_request')->name('otp_request');
        Route::patch('/default-erase', 'default_erase')->name('default_erase');


        Route::post('/password-reset-while-connected-init', 'password_reset_while_connected_init')->name('password_reset_while_connected_init');
        Route::patch('/password-reset-while-connected-process', 'password_reset_while_connected_process')->name('password_reset_while_connected_process');


        Route::post('/email-reset-init', 'email_reset_init')->name('email_reset_init');
        Route::patch('/email-reset-process', 'email_reset_process')->name('email_reset_process');


        Route::delete('/logout', 'logout')->name('logout');


    });

/**
 *
 *
 *
 *
 *
 *ROUTES LIEES AUX FONCTIONNALITES  DU SUPERADMIN
 *
 *
 *
 *
 *
 */

Route::controller(\App\Http\Controllers\SuperAdmin\GestionAdminController::class)
    ->name('superAdmin.')
    ->middleware('superadmin')
    ->prefix('superadmin')
    ->group(function ()
    {
        Route::get('admin-list', 'admin_list')->name('admin_list');

        Route::post('admin-create', 'admin_create')->name('admin_create');

        Route::patch('admin-edit', 'admin_edit')->name('admin_edit');

        Route::delete('admin-delete', 'admin_delete')->name('admin_delete');
    });





/**
 *
 *
 *
 *
 *
 *ROUTES LIEES A L'AUTHENTIFICATION DE L'ADMIN
 *
 *
 *
 *
 *
 */


/**
 * ROUTES HORS CONNEXION
 */

Route::controller(\App\Http\Controllers\Admin\AdminAuthController::class)
    ->name('admin.')
    ->prefix('admin')
    ->group(function (){

        Route::post('/signin-init', 'signin_init')->name('signin_init');
        Route::patch('/signin-process', 'signin_process')->name('signin_process');


        Route::post('/login', 'login')->name('login');


        Route::post('/password-reset-while-dissconnected-init', 'password_reset_while_dissconnected_init')->name('password_reset_while_dissconnected_init');
        Route::patch('/password-reset-while-dissconnected-process', 'password_reset_while_dissconnected_process')->name('password_reset_while_dissconnected_process');



    });

/**
 *ROUTES AVEC CONNEXION
 */
Route::controller(\App\Http\Controllers\Admin\AdminAuthController::class)
    ->name('admin.')
    ->middleware('admin')
    ->prefix('admin')
    ->group(function ()
    {


        Route::delete('/logout', 'logout')->name('logout');

        Route::post('/password-reset-while-connected-init', 'password_reset_while_connected_init')->name('password_reset_while_connected_init');
        Route::patch('/password-reset-while-connected-process', 'password_reset_while_connected_process')->name('password_reset_while_connected_process');


        Route::post('/email-reset-init', 'email_reset_init')->name('email_reset_init');
        Route::patch('/email-reset-process', 'email_reset_process')->name('email_reset_process');




    });

/**
 *
 *
 *
 *
 *
 *ROUTES LIEES AUX FONCTIONNALITES  DE L'ADMIN
 *
 *
 *
 *
 *
 */


/**
 *GESTION DES GARDES MALADES
 */

Route::controller(\App\Http\Controllers\Admin\AdminManageSickGuardController::class)
    ->name('admin.')
    ->middleware('admin')
    ->prefix('admin')
    ->group(function ()
    {
        Route::get('/sickguard-list', 'sickGuardList')->name('sickGuardList');

        Route::get('/sickguard-ban-list', 'sickGuardBanList')->name('sickGuardBanList');

        Route::patch('/sickguard-accept', 'sickGuardAccept')->name('sickGuardAccept');

        Route::patch('/sickguard-reject', 'sickGuardReject')->name('sickGuardReject');

        Route::patch('/sickguard-ban', 'sickGuardBan')->name('sickGuardBan');

        Route::patch('/sickguard-unban', 'sickGuardUnBan')->name('sickGuardUnBan');


    });






/**
 *GESTION DES CLIENTS
 */

Route::controller(\App\Http\Controllers\Admin\AdminManageClientController::class)
    ->name('admin.')
    ->middleware('admin')
    ->prefix('admin')
    ->group(function ()
    {
        Route::get('/client-list', 'clientList')->name('clientList');

        Route::get('/client-ban-list', 'clientBanList')->name('clientBanList');

        Route::patch('/client-ban', 'clientBan')->name('clientBan');

        Route::patch('/client-unban', 'clientUnBan')->name('clientUnBan');


    });




/**
 *
 *
 *
 *
 *
 *ROUTES LIEES A L'AUTHENTIFICATION DU SICKGUARD
 *
 *
 *
 *
 *
 */


/**
 * ROUTES HORS CONNEXION
 */

Route::controller(App\Http\Controllers\SickGuard\AuthSickGuardController::class)
    ->name('sickguard.')
    ->prefix('sickguard')
    ->group(function (){

        Route::post('/signin-init', 'signin_init')->name('signin_init');
        Route::post('/signin-process', 'signin_process')->name('signin_process');


        Route::post('/login', 'login')->name('login');


        Route::post('/password-reset-while-dissconnected-init', 'password_reset_while_dissconnected_init')->name('password_reset_while_dissconnected_init');
        Route::patch('/password-reset-while-dissconnected-process', 'password_reset_while_dissconnected_process')->name('password_reset_while_dissconnected_process');



    });

/**
 *ROUTES AVEC CONNEXION
 */
Route::controller(App\Http\Controllers\SickGuard\AuthSickGuardController::class)
    ->name('sickguard.')
    ->middleware('sickguard')
    ->prefix('sickguard')
    ->group(function ()
    {


        Route::delete('/logout', 'logout')->name('logout');

        Route::post('/password-reset-while-connected-init', 'password_reset_while_connected_init')->name('password_reset_while_connected_init');
        Route::patch('/password-reset-while-connected-process', 'password_reset_while_connected_process')->name('password_reset_while_connected_process');


        Route::post('/email-reset-init', 'email_reset_init')->name('email_reset_init');
        Route::patch('/email-reset-process', 'email_reset_process')->name('email_reset_process');




    });

/**
 *
 *
 *
 *
 *
 *ROUTES LIÉES AUX FONCTIONNALITÉS  DE GESTION DE PROFIL  SICK-GUARD
 *
 *
 *
 *
 *
 */


Route::name('sickguard.profil.')
    ->prefix('sickguard/profil/')
    ->group(function ()
    {

        /**
         * GESTION DES EXPERIENCES
         */

        Route::name('experience.')
            ->prefix('experience')
            ->controller(\App\Http\Controllers\SickGuard\Profil\SickGuardGestionExperienceController::class)
            ->group(function () {
                Route::get('list', 'list')->name('list');
                Route::post('create', 'create')->name('create');
                Route::delete('delete', 'delete')->name('delete');
            });


        /**
         * GESTION DES EXPERIENCES
         */

        Route::name('qualification.')
            ->prefix('qualification')
            ->controller(\App\Http\Controllers\SickGuard\Profil\SickGuardGestionQualificationController::class)
            ->group(function () {
                Route::get('list', 'list')->name('list');
                Route::post('create', 'create')->name('create');
                Route::delete('delete', 'delete')->name('delete');
            });
    });




/**
 *
 *
 *
 *
 *
 *ROUTES LIÉES AUX FONCTIONNALITÉS  DU WORKFLOW DU SICK-GUARD
 *             (DISPONIBILITÉS , SERVICES , MISSIONS)
 *
 *
 *
 *
 *
 */


Route::name('sickguard.workflow.')
    ->prefix('sickguard/workflow')
    ->middleware('sickguard')
    ->group(function () {

        /**
         * GESTION DES DISPONIBILITÉS
         */

        Route::name('disponibilite.')
            ->prefix('disponibilite')
            ->controller(\App\Http\Controllers\SickGuard\Workflow\SickGuardGestionDisponibiliteController::class)
            ->group(function () {
                Route::get('list', 'list')->name('list');
                Route::post('create', 'create')->name('create');
                Route::delete('delete', 'delete')->name('delete');
            });

    });







/**
 *
 *
 *
 *
 *
 *ROUTES LIEES A L'AUTHENTIFICATION DU CLIENT
 *
 *
 *
 *
 *
 */


/**
 * ROUTES HORS CONNEXION
 */

Route::controller(App\Http\Controllers\Client\ClientAuthController::class)
    ->name('client.')
    ->prefix('client')
    ->group(function (){

        Route::post('/signin-init', 'signin_init')->name('signin_init');
        Route::post('/signin-process', 'signin_process')->name('signin_process');


        Route::post('/login', 'login')->name('login');


        Route::post('/password-reset-while-dissconnected-init', 'password_reset_while_dissconnected_init')->name('password_reset_while_dissconnected_init');
        Route::patch('/password-reset-while-dissconnected-process', 'password_reset_while_dissconnected_process')->name('password_reset_while_dissconnected_process');



    });

/**
 *ROUTES AVEC CONNEXION
 */
Route::controller(App\Http\Controllers\Client\ClientAuthController::class)
    ->name('client.')
    ->middleware('client')
    ->prefix('client')
    ->group(function ()
    {


        Route::delete('/logout', 'logout')->name('logout');

        Route::post('/password-reset-while-connected-init', 'password_reset_while_connected_init')->name('password_reset_while_connected_init');
        Route::patch('/password-reset-while-connected-process', 'password_reset_while_connected_process')->name('password_reset_while_connected_process');


        Route::post('/email-reset-init', 'email_reset_init')->name('email_reset_init');
        Route::patch('/email-reset-process', 'email_reset_process')->name('email_reset_process');




    });

/**
 *
 *
 *
 *
 *
 *ROUTES LIEES AUX FONCTIONNALITES  DU CLIENT
 *
 *
 *
 *
 *
 */

Route::controller(App\Http\Controllers\Client\ClientAuthController::class)
    ->name('client.')
    ->middleware('client')
    ->prefix('client')
    ->group(function ()
    {

    });





