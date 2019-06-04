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
    return view('welcome');
});

// Index of sports
Route::get('sports',function () {
    return view('sports');
});

// Index of reports
Route::get('reports',function () {
    return view('reports');
});

// Index of sports manual email send
Route::get('send',function () {
    return view('send');
});

// NFL Data
//Route::get('nfl/data','SportsDataController@rawData');
Route::get('nfl/email','NflDataController@showEmailFormatDataPlainText');
Route::get('nfl/tomorrow/email','NflDataController@showEmailFormatDataTomorrowPlainText');
Route::get('nfl/yesterday/email','NflDataController@showEmailFormatDataYesterdayPlainText');
Route::get('nfl/thisweek/data','FootballScheduleController@showNflSchedulePlainText');
Route::get('nfl/nextweek/data','FootballScheduleController@showNextNflSchedulePlainText');
Route::get('reports/roundball/nfl','NflDataController@showRoundBallPlainText');
Route::get('nfl/send','MailTest@sendNfl');
Route::get('nfl/tomorrow/send','MailTest@sendNflTomorrow');
Route::get('nfl/yesterday/send','MailTest@sendNflYesterday');
Route::get('nfl/thisweek/send','MailTest@sendNflThisWeek');
Route::get('nfl/nextweek/send','MailTest@sendNflNextWeek');

// NCAA FB Data
//Route::get('ncaa/fb/data','NcaafbController@rawData');
Route::get('ncaa/fb/email','NcaafbController@showEmailFormatDataPlainText');
Route::get('ncaa/fb/tomorrow/email','NcaafbController@showEmailFormatDataTomorrowPlainText');
Route::get('ncaa/fb/yesterday/email','NcaafbController@showEmailFormatDataYesterdayPlainText');
Route::get('ncaa/fb/thisweek/data','FootballScheduleController@showNcaaFbSchedulePlainText');
Route::get('ncaa/fb/nextweek/data','FootballScheduleController@showNextNcaaFbSchedulePlainText');
Route::get('ncaa/fb/bowls/data','FootballScheduleController@showNcaaFbBowlSchedulePlainText');
Route::get('reports/roundball/ncaa/fb','NcaafbController@showRoundBallPlainText');
Route::get('ncaa/fb/send','MailTest@sendNcaafb');
Route::get('ncaa/fb/tomorrow/send','MailTest@sendNcaafbTomorrow');
Route::get('ncaa/fb/yesterday/send','MailTest@sendNcaafbYesterday');
Route::get('ncaa/fb/thisweek/send','MailTest@sendNcaafbThisWeek');
Route::get('ncaa/fb/nextweek/send','MailTest@sendNcaafbNextWeek');
Route::get('ncaa/fb/bowls/send','MailTest@sendNcaafbBowls');

// NBA Data
//Route::get('nba/data','NbaDataController@rawData');
Route::get('nba/email','NbaDataController@showEmailFormatDataPlainText');
Route::get('nba/tomorrow/email','NbaDataController@showEmailFormatDataTomorrowPlainText');
Route::get('nba/yesterday/email','NbaDataController@showEmailFormatDataYesterdayPlainText');
Route::get('reports/roundball/nba','NbaDataController@showRoundBallPlainText');
Route::get('nba/send','MailTest@sendNba');
Route::get('nba/tomorrow/send','MailTest@sendNbaTomorrow');
Route::get('nba/yesterday/send','MailTest@sendNbaYesterday');


// NCAA Hoops Data
//Route::get('ncaa/bb/data','NcaabbDataController@rawData');
Route::get('ncaa/bb/email','NcaabbDataController@showEmailFormatDataPlainText');
Route::get('ncaa/bb/tomorrow/email','NcaabbDataController@showEmailFormatDataTomorrowPlainText');
Route::get('ncaa/bb/yesterday/email','NcaabbDataController@showEmailFormatDataYesterdayPlainText');
Route::get('reports/roundball/ncaa/bb','NcaabbDataController@showRoundBallPlainText');
Route::get('ncaa/bb/send','MailTest@sendNcaabb');
Route::get('ncaa/bb/tomorrow/send','MailTest@sendNcaabbTomorrow');
Route::get('ncaa/bb/yesterday/send','MailTest@sendNcaabbYesterday');

// NHL Data
//Route::get('nhl/data','NhlDataController@rawData');
Route::get('nhl/email','NhlDataController@showEmailFormatDataPlainText');
Route::get('nhl/tomorrow/email','NhlDataController@showEmailFormatDataTomorrowPlainText');
Route::get('nhl/yesterday/email','NhlDataController@showEmailFormatDataYesterdayPlainText');
Route::get('reports/roundball/nhl','NhlDataController@showRoundBallPlainText');
Route::get('nhl/send','MailTest@sendNhl');
Route::get('nhl/yesterday/send','MailTest@sendNhlYesterday');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// CORRLINKS
Route::get('corrlinks/index','CorrLinksController@connect');
Route::post('corrlinks/index','CorrLinksController@connect');
