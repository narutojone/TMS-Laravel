<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/invitation/{invitation}', 'InvitationController@register')->middleware('guest');
Route::post('/invitation/{invitation}', 'InvitationController@submit')->middleware('guest');

// Twilio
Route::prefix('twilio')->group(function () {
    Route::post('incoming-call', 'TwilioController@incomingCall');
    Route::post('process-gather/{client_id}/{employee_id}', 'TwilioController@processGather');
    Route::post('record/{number}/{fallBackNumber?}', 'TwilioController@record');
    Route::post('record-callback/{number}/', 'TwilioController@recordCallback');
});

//Allow client to rate us
Route::get('ratings/{hash}/{rate}', 'RatingController@store')->name('ratings.store');
Route::get('ratings/{hash}/feedback/{rate}/create', 'RatingFeedbackController@create')->name('ratings.feedback.create');
Route::post('ratings/{hash}/feedback/{rate}', 'RatingFeedbackController@store')->name('ratings.feedback.store');

// Allow client to mark task as delivered
Route::get('/tasks/{task}/delivered', 'TaskController@createDelivered')->name('tasks.delivered.create');
Route::get('/tasks/delivered/{hash}', 'TaskController@createDeliveredFromHash')->name('tasks.delivered.create.from.hash');
Route::post('/tasks/{task}/delivered/remove', 'TaskController@removeDelivered')->name('tasks.delivered.destroy');
Route::post('/tasks/delivered/{hash}/remove', 'TaskController@removeDeliveredFromHash')->name('tasks.delivered.destroy.from.hash');

// Get public images from WYSIWYG editor
Route::get('/editor/images/public/{fileName}', 'EditorController@getPublicImage')->name('editor.file.public.get');
