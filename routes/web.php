<?php

use Illuminate\Support\Facades\Route;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {



        /** ROUTES SUPER-ADMIN */

        Route::name('super-admin.')
            ->prefix('super-admin')
            ->group(function (){

                /** AUTHENTICATION  */

                Route::name('auth.')
                    ->prefix('auth')
                    ->controller(App\Http\Controllers\SuperAdmin\AuthController::class)
                    ->group(function (){

                        /**  WHILE DISCONNECTED */

                        Route::name('disconnected.')
                            ->prefix('disconnected')
                            ->group(function (){
                                Route::get('login','loginView')->name('loginView');
                                Route::post('login','login')->name('login');
                            });


                        /**  WHILE CONNECTED */

                        Route::name('connected.')
                            ->prefix('connected')
                            ->middleware([ App\Http\Middleware\SuperAdminMiddleware::class ])
                            ->group(function (){
                                Route::delete('logout','logout')->name('logout');
                            });
                    });






                /** SIMPLES ROUTES */

                Route::controller(App\Http\Controllers\SuperAdmin\BaseController::class)
                    ->middleware([ App\Http\Middleware\SuperAdminMiddleware::class ])
                    ->group(function (){
                        Route::get('/','profileView')->name('profileView');
                        Route::get('/profile','profileView')->name('profileView');
                        Route::get('/manage-admins','manageAdminsView')->name('manageAdminsView');
                    });





            });




        /** ROUTES ADMIN */

        Route::name('admin.')
            ->prefix('admin')
            ->group(function (){

                /** AUTHENTICATION  */

                Route::name('auth.')
                    ->prefix('auth')
                    ->controller(App\Http\Controllers\Admin\AuthController::class)
                    ->group(function (){

                        /**  WHILE DISCONNECTED */

                        Route::name('disconnected.')
                            ->prefix('disconnected')
                            ->group(function (){
                                Route::get('login','loginView')->name('loginView');
                                Route::post('login','login')->name('login');

                                Route::get('signup','signupView')->name('signupView');
                                Route::post('signup','signup')->name('signup');
                            });


                        /**  WHILE CONNECTED */

                        Route::name('connected.')
                            ->prefix('connected')
                            ->middleware([ App\Http\Middleware\AdminMiddleware::class ])
                            ->group(function (){
                                Route::delete('logout','logout')->name('logout');
                            });
                    });






                /** SIMPLES ROUTES */

                Route::controller(App\Http\Controllers\Admin\BaseController::class)
                    ->middleware([ App\Http\Middleware\AdminMiddleware::class ])
                    ->group(function (){
                        Route::get('/','profileView')->name('profileView');
                        Route::get('/profile','profileView')->name('profileView');
                        Route::get('/manageOrganizer','manageOrganizerView')->name('manageOrganizerView');
                        Route::get('/manageOrganizations','manageOrganizationsView')->name('manageOrganizationsView');
                        Route::get('/manageOrganizerOrganizationsView-{organizer}','manageOrganizerOrganizationsView')->name('manageOrganizerOrganizationsView');

                    });





            });




        /** ROUTES  ORGANIZATION */

        Route::name('organization.')
            ->group(function (){


                /** ROUTES  ORGANIZER */


                Route::name('organizer.')
                    ->prefix('organizer')
                    ->group(function (){

                        /** AUTHENTICATION  */

                        Route::name('auth.')
                            ->prefix('auth')
                            ->controller(App\Http\Controllers\Organizer\AuthController::class)
                            ->group(function (){

                                /**  WHILE DISCONNECTED */

                                Route::name('disconnected.')
                                    ->prefix('disconnected')
                                    ->group(function (){
                                        Route::get('login','loginView')->name('loginView');
                                        Route::post('login','login')->name('login');

                                        Route::get('signup','signupView')->name('signupView');
                                        Route::post('signup','signup')->name('signup');
                                    });


                                /**  WHILE CONNECTED */

                                Route::name('connected.')
                                    ->prefix('connected')
                                    ->middleware([ App\Http\Middleware\OrganizerMiddleware::class ])
                                    ->group(function (){
                                        Route::delete('logout','logout')->name('logout');
                                    });
                            });






                        /** SIMPLES ROUTES */

                        Route::controller(App\Http\Controllers\Organizer\BaseController::class)
                            ->middleware([ App\Http\Middleware\OrganizerMiddleware::class ])
                            ->group(function (){
                                Route::get('/','profileView')->name('profileView');
                                Route::get('/profile','profileView')->name('profileView');
                                Route::get('/organizations','organizationsView')->name('organizationsView');
                            });





                    });

            });








    });
}
