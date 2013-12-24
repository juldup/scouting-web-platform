<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', "AccueilController@showPage");
Route::get('gestion/accueil', "AccueilController@showGestion");

Route::get('calendrier', "CalendrierController@showPage");
Route::get('gestion/calendrier', "CalendrierController@showGestion");
