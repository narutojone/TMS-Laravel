<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([], function () {
    // User
    Route::get('/users', 'UserController@index');
    Route::post('/users', 'UserController@store');
    Route::get('/users/{user}', 'UserController@show');
    Route::get('/users/{user}/clients', 'UserController@clients');
    Route::post('/users/{user}', 'UserController@update');
    Route::post('/users/{user}/activate', 'UserController@activate');
    Route::post('/users/{user}/deactivate', 'UserController@deactivate');

    // FaqCategoryController
    Route::get('/faq-categories/{faqCategory}', 'FaqCategoryController@show');
    Route::post('/system_settings/faq-categories', 'FaqCategoryController@store');
    Route::post('/faq-categories/{faqCategory}/move', 'FaqCategoryController@move');
    Route::delete('/faq-categories/{faqCategory}', 'FaqCategoryController@destroy');

    // FaqController
    Route::get('/faq/{faq}', 'FaqController@show');
    Route::post('/faq/{faq}', 'FaqController@update');
    Route::post('/faq', 'FaqController@store');
    Route::delete('/faq/{faq}', 'FaqController@destroy');
    Route::post('/faq/{faq}/move', 'FaqController@move');

    // Template
    Route::get('/templates', 'TemplateController@index');
    Route::post('/templates', 'TemplateController@store');
    Route::get('/templates/{template}', 'TemplateController@show');
    Route::post('/templates/{template}', 'TemplateController@update');

    // OverdueReason Controller - not used at this time (code is available)
    // Route::post('/system_settings/overdue', 'OverdueReasonController@store');
    // Route::post('/system_settings/overdue/{reason}', 'OverdueReasonController@update');
    // Route::delete('/system_settings/overdue/{reason}', 'OverdueReasonController@delete');
    // Route::post('/system_settings/overdue/{reason}/move', 'OverdueReasonController@move');

    // Task Subtask
    Route::post('/subtasks/{subtask}/reopen', 'ReopenSubtaskController@submit')->middleware('can:reopen,subtask');

    // Flags
    Route::get('/system_settings/flags', 'FlagsController@index')->name('settings.flags.index');
    Route::post('/system_settings/flags', 'FlagsController@store')->name('settings.flags.store');
    Route::patch('/system_settings/flags/{flag}', 'FlagsController@update')->name('settings.flags.update');
    Route::delete('/system_settings/flags/{flag}', 'FlagsController@destroy')->name('settings.flags.destroy');

    // Task Comment
    Route::get('/tasks/{task}/comments', 'CommentController@index')->name('task.comments.index');
    Route::post('/tasks/{task}/comments', 'CommentController@store')->name('task.comments.store');
    Route::get('/comments/{comment}', 'CommentController@show')->name('task.comments.show');

    // Information Controller
    Route::get('/system_settings/users_information', 'InformationController@index')->name('settings.information.index');
    Route::get('/system_settings/users_information/{information}', 'InformationController@show')->name('settings.information.show');
    Route::post('/system_settings/users_information', 'InformationController@store')->name('settings.information.store');
    Route::post('/system_settings/users_information/{information}', 'InformationController@update')->name('settings.information.update');
    Route::delete('/system_settings/users_information/{information}', 'InformationController@destroy')->name('settings.information.destroy');
    
    // User Information
    Route::get('/information', 'UserInformationController@index')->name('information.index');
    Route::get('/information/{information}', 'UserInformationController@show')->name('information.show');

    // Task
    Route::post('/tasks/{task}/reopen', 'ReopenController@submit');
    Route::post('/clients/{client}/tasks', 'TaskController@store');
    Route::get('/tasks/{task}', 'TaskController@show');
    Route::post('/tasks/{task}', 'TaskController@update');

    // Client
    Route::post('/clients/{client}', 'ClientController@update');
    Route::post('/clients', 'ClientController@store');
    // Average client rating done to users
    Route::get('/average-rating', 'RatingController@index');
});

// Client
Route::get('/clients', 'ClientController@index');
Route::get('/clients/{client}', 'ClientController@show');


// Task
Route::get('/clients/{client}/tasks', 'TaskController@index');
Route::delete('/tasks/{task}', 'TaskController@destroy')->middleware('can:delete,task');

// Task Subtask
Route::get('/tasks/{task}/subtasks', 'SubtaskController@index');
Route::get('/subtasks/{subtask}', 'SubtaskController@show');

// Template User
// Route::get('/templates/{template}/users', 'TemplateUserController@index');               // DEPRECATED
// Route::post('/templates/{template}/users', 'TemplateUserController@store');              // DEPRECATED
// Route::delete('/templates/{template}/users/{user}', 'TemplateUserController@destroy');   // DEPRECATED

// Invitations
//Route::get('/invitations', 'InvitationController@index');                                 // DEPRECATED
//Route::delete('/invitations/{invitation}', 'InvitationController@destroy');               // DEPRECATED

// List all information to accept
Route::get('/list_information/', 'ListInformationController@index')->name('information.list');


