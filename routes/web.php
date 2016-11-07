<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->middleware(['web']);

Route::auth();
Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/home', function(){
    return redirect(route('dashboard'));
});

Route::group(['prefix'=>'team'], function(){
    Route::put('/create', 'TeamController@doCreate')->name('teams.doCreate');
    Route::get('/create', 'TeamController@showCreate')->name('teams.create');
    Route::get('/{number}', 'TeamController@viewTeam')->name('teams.show');
    Route::get('/{number}/manage', 'TeamController@manageTeam')->name('teams.manage');
    Route::get('/acceptInviteTeam/{teamId}', 'TeamController@postAcceptTeamInvite')->name('teams.teamAcceptInvite');
    Route::get('/acceptInvite/{inviteNumber}', 'TeamController@acceptTeamInvite')->name('teams.acceptInvite');
});
Route::get('/teams', 'TeamController@showAllTeams')->name('teams.all');


// Profile routes
Route::group(['prefix'=>'profile'], function(){
    Route::get('/edit', 'ProfileController@edit')->name('profile.edit')->middleware(['auth']);
    Route::get('/oauth', 'ProfileController@oauthPanel')->name('profile.oauth')->middleware(['auth']);
    Route::patch('/edit', 'ProfileController@update')->name('profile.update')->middleware(['auth']);
    Route::delete('/edit', 'ProfileController@delete')->middleware(['auth']);
    Route::get('/image/{image}/{size}', 'ProfileController@image')->name('profile.image');
    Route::get('/{number}', 'ProfileController@profile')->name('profile.show');
});

// Survey routes
Route::group(['prefix'=>'survey'], function(){
    Route::get('/edit/{surveyId}', 'SurveyController@edit')->name('survey.edit');
    Route::get('/questions/{surveyId}', 'SurveyController@questions')->name('survey.questions');
    Route::get('/{survey}', 'SurveyController@showSurvey')->name('survey.view');
    Route::put('/{survey}/submit', 'SurveyController@submitSurvey')->name('survey.submit');
});
