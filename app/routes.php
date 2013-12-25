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

// Views

View::composer('menu.menu', "MenuComposer");
View::composer('menu.user_box', "UserBoxComposer");
View::composer('menu.tabs', "TabsComposer");

// Routes

Route::get('login', array("as" => "login", "uses" => "UserController@login"));
Route::get('logout', array("as" => "logout", "uses" => "UserController@logout"));
Route::get('modifier-utilisateur', array("as" => "edit_user", "uses" => "UserController@editUser"));
Route::get('recuperer-mot-de-passe', array("as" => "retrieve_password", "uses" => "UserController@retrievePassword"));
Route::get('nouvel-utilisateur', array("as" => "create_user", "uses" => "UserController@create"));

Route::get('/', array("as" => "home", "uses" => "HomeController@showPage"));
Route::get('gestion/accueil', array("as" => "manage_home", "uses" => "HomeController@showGestion"));
Route::post('gestion/accueil', array("as" => "manage_home", "uses" => "HomeController@savePage"));

Route::get('calendrier/{section_slug?}', array("as" => "calendar", "uses" => "CalendarController@showPage"));
Route::get('gestion/calendrier', array("as" => "calendar_gestion", "uses" => "CalendarController@showGestion"));

Route::get('sections', array("as" => "sections", "uses" => "SectionController@showPage"));
Route::get('gestion/sections', "SectionController@showGestion");

Route::get('adresses', array("as" => "addresses", "uses" => "AddressController@showPage"));
Route::get('gestion/adresses', "AddressController@showGestion");

Route::get('contacts', array("as" => "contacts", "uses" => "ContactController@showPage"));
Route::get('gestion/contacts', "ContactController@showGestion");

