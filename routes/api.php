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
        Route::put('register' , 'UserController@register');//in user register api you musn't be authenticated for register
        Route::post('login' , 'UserController@login');
    });

    Route::middleware('auth:api' , 'checkPassword')->group(function(){
        Route::post('updateUser' , 'UserController@updateUser');
        Route::delete('deleteUser' , 'UserController@deleteUser');
        Route::get('searchForUser' , 'UserController@searchForUser');
        Route::get('roleValidator', 'UserController@roleValidator');
    });
});

/*
 Task API
*/
Route::middleware('auth:api' , 'checkPassword')->namespace('App\Http\Controllers\api')->group(function(){
    Route::post('createTask' , 'TaskController@createTask');
    Route::post('updateTask' , 'TaskController@updateTask');
    Route::delete('deleteTask' , 'TaskController@delete');
    Route::get('getUserTasks' , 'TaskController@getUserTasks');
});

/*
  Challenge API
*/

Route::middleware('auth:api' , 'checkPassword')->namespace('App\Http\Controllers\api')->group(function(){
    Route::put('createChallenge' , 'ChallengeController@createChallenge');
    Route::post('updateChallenge' , 'ChallengeController@updateChallenge');
    Route::post('updateChallengePoints' , 'ChallengeController@updateChallengePoints');
    Route::delete('deleteChallenge' , 'ChallengeController@deleteChallenge');
});


/*
  Session API
*/
Route::middleware('auth:api' , 'checkPassword')->namespace('App\Http\Controllers\api')->group(function(){
    Route::put('createSession' , 'SessionController@createSession');
    Route::get('getAllSessions' , 'SessionController@getAllSessions');
    Route::post('updateSessionPoints' , 'SessionController@updateSessionPoints');
    Route::delete('deleteSession' , 'SessionController@deleteSession');
    Route::delete('deleteAllSessions' , 'SessionController@deleteAllSessions');
});

/*
  Base Task API
*/
Route::middleware('auth:api' , 'checkPassword')->namespace('App\Http\Controllers\api')->group(function(){
    Route::put('createBaseTask' , 'BaseTaskController@createBaseTask');
    Route::post('updateBaseTaskPoints' , 'BaseTaskController@updateBaseTaskPoints');
    Route::get('getBaseTasks' , 'BaseTaskController@getBaseTasks');
    Route::delete('deleteBaseTasks' , 'BaseTaskController@deleteBaseTasks');
});
