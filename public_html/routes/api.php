<?php

use Illuminate\Support\Facades\Route;

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

/*
  User API
*/
Route::namespace('App\Http\Controllers\api')->group(function(){
    Route::middleware('api' , 'checkPassword')->group(function(){
        Route::post('register' , 'UserController@register');//in user register api you musn't be authenticated for register
        Route::post('login' , 'UserController@login');
        Route::get('searchForUser' , 'UserController@searchForUser');
    });

    Route::middleware('auth:api' , 'checkPassword')->group(function(){
        Route::post('updateUser' , 'UserController@updateUser');
        Route::post('deleteUser' , 'UserController@deleteUser');
        Route::get('roleValidator', 'UserController@roleValidator');
        Route::post('findUserWithToken' , 'UserController@findUserWithToken');
    });
});

/*
  Challenge API
*/

Route::middleware('auth:api' , 'checkPassword')->namespace('App\Http\Controllers\api')->group(function(){
    Route::post('createChallenge' , 'ChallengeController@createChallenge');
    Route::post('updateChallenge' , 'ChallengeController@updateChallenge');
    Route::post('deleteChallenge' , 'ChallengeController@deleteChallenge');
    Route::get('getChallenge' , 'ChallengeController@getChallenge');
    Route::get('getTrandingChallenges' , 'ChallengeController@getTrandingChallenges');
});


/*
  Session API
*/
Route::middleware('auth:api' , 'checkPassword')->namespace('App\Http\Controllers\api')->group(function(){
    Route::post('createSession' , 'SessionController@createSession');
    Route::get('getAllSessions' , 'SessionController@getAllSessions');
    Route::post('updateSessionPoints' , 'SessionController@updateSessionPoints');
    Route::post('deleteSession' , 'SessionController@deleteSession');
    Route::post('deleteAllSessions' , 'SessionController@deleteAllSessions');
});

/*
  Base Task API
*/
Route::middleware('auth:api' , 'checkPassword')->namespace('App\Http\Controllers\api')->group(function(){
    Route::post('createBaseTask' , 'BaseTaskController@createBaseTask');
    Route::post('updateBaseTaskPoints' , 'BaseTaskController@updateBaseTaskPoints');
    Route::get('getBaseTasks' , 'BaseTaskController@getBaseTasks');
    Route::post('deleteBaseTasks' , 'BaseTaskController@deleteBaseTasks');
});
