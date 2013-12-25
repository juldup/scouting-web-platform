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

View::composer('menu', "MenuComposer");
View::composer('member_box', "MemberBoxComposer");

// Routes

Route::get('login', array("as" => "login", "uses" => "MemberController@login"));
Route::get('logout', array("as" => "logout", "uses" => "MemberController@logout"));
Route::get('modifier-visiteur', array("as" => "edit_member", "uses" => "MemberController@editMember"));
Route::get('recuperer-mot-de-passe', array("as" => "retrieve_password", "uses" => "MemberController@retrievePassword"));
Route::get('nouvel-utilisateur', array("as" => "create_member", "uses" => "MemberController@create"));

Route::get('/', array("as" => "home", "uses" => "HomeController@showPage"));
Route::get('gestion/accueil', "HomeController@showGestion");

Route::get('calendrier', array("as" => "calendar", "uses" => "CalendarController@showPage"));
Route::get('gestion/calendrier', array("as" => "calendar_gestion", "uses" => "CalendarController@showGestion"));

Route::get('sections', array("as" => "sections", "uses" => "SectionController@showPage"));
Route::get('gestion/sections', "SectionController@showGestion");

Route::get('adresses', array("as" => "addresses", "uses" => "AddressController@showPage"));
Route::get('gestion/adresses', "AddressController@showGestion");

Route::get('contacts', array("as" => "contacts", "uses" => "ContactController@showPage"));
Route::get('gestion/contacts', "ContactController@showGestion");

