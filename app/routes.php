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

Route::get('login/{section_slug?}', array("as" => "login", "uses" => "UserController@login"));
Route::get('logout/{section_slug?}', array("as" => "logout", "uses" => "UserController@logout"));
Route::get('modifier-utilisateur/{section_slug?}', array("as" => "edit_user", "uses" => "UserController@editUser"));
Route::get('recuperer-mot-de-passe/{section_slug?}', array("as" => "retrieve_password", "uses" => "UserController@retrievePassword"));
Route::get('nouvel-utilisateur/{section_slug?}', array("as" => "create_user", "uses" => "UserController@create"));

// Sections
Route::get('section/{section_slug?}', array("as" => "section", "uses" => "SectionController@showPage"));
Route::get('gestion/section/{section_slug}', array("as" => "manage_section", "uses" => "SectionController@showEdit"));
Route::post('gestion/section/{section_slug}', array("as" => "manage_section", "uses" => "SectionController@savePage"));

// Addresses
Route::get('adresses/{section_slug?}', array("as" => "addresses", "uses" => "AddressesController@showPage"));
Route::get('gestion/adresses/{section_slug?}', array("as" => "manage_addresses", "uses" => "AddressesController@showEdit"));
Route::post('gestion/adresses/{section_slug?}', array("as" => "manage_addresses", "uses" => "AddressesController@savePage"));

// Contacts
Route::get('contacts/{section_slug?}', array("as" => "contacts", "uses" => "ContactController@showPage"));
Route::get('gestion/contacts/{section_slug?}', "ContactController@showEdit");

// Annual feast

// Registration

// Health card

// Unit policy
Route::get('charte/{section_slug?}', array("as" => "unit_policy", "uses" => "UnitPolicyController@showPage"));
Route::get('gestion/charte/{section_slug?}', array("as" => "manage_unit_policy", "uses" => "UnitPolicyController@showEdit"));
Route::post('gestion/charte/{section_slug?}', array("as" => "manage_unit_policy", "uses" => "UnitPolicyController@savePage"));

// Uniforms
Route::get('uniforme/{section_slug?}', array("as" => "uniform", "uses" => "UniformController@showPage"));
Route::get('gestion/uniforme/{section_slug}', array("as" => "manage_uniform", "uses" => "UniformController@showEdit"));
Route::post('gestion/uniforme/{section_slug}', array("as" => "manage_uniform", "uses" => "UniformController@savePage"));

// Links

// News

// Calendar
Route::get('calendrier/{section_slug?}', array("as" => "calendar", "uses" => "CalendarController@showPage"));
Route::get('gestion/calendrier', array("as" => "calendar_gestion", "uses" => "CalendarController@showEdit"));

// Download

// E-mails

// Photos

// Leaders

// Listing

// Suggestions

// Guest book

// Help

// Home
Route::get('/{section_slug?}', array("as" => "home", "uses" => "HomeController@showPage"));
Route::get('gestion/accueil/{section_slug?}', array("as" => "manage_home", "uses" => "HomeController@showEdit"));
Route::post('gestion/accueil/{section_slug}', array("as" => "manage_home", "uses" => "HomeController@savePage"));
