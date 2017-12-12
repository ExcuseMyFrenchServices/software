<?php

use Illuminate\Support\Facades\Route;

Route::get('/', ['middleware' => 'auth', function () {
    return view('home');
}]);

Route::post('login', 'SessionController@login');
Route::get('logout', 'SessionController@logout');

// USER

Route::resource('user', 'UserController');

Route::get('user/{user}/credentials/edit', 'UserController@editCredentialsForm');
Route::get('user/{user}/password', 'UserController@passwordEditForm');
Route::get('reset/{hash}', 'UserController@passwordEdit');
Route::get('user/archive/{user}', 'UserController@archive');
Route::get('user/unarchive/{user}', 'UserController@unarchive');

Route::post('user/{user}/credentials/edit', 'UserController@editCredentials');
Route::post('user/{user}/password', 'UserController@passwordUpdate');

Route::get('payroll/{user}', 'UserController@payroll');
Route::get('payroll/{user}/{week}', 'UserController@payroll');

Route::get('archive', 'UserController@archiveIndex');

	//Search functionnality
Route::post('user/search', 'UserController@search');

// EVENTS

Route::resource('event', 'EventController');

Route::post('/preBooking', 'EventController@preBooking');

Route::post('event/changeUserRole', 'EventController@changeUserRole');

Route::get('events/{user}', 'EventController@events');
Route::get('past/events', 'EventController@indexPast');
Route::get('assignment/add/{event}/{time}', 'EventController@staff');
Route::get('confirm/{hash}', 'EventController@confirmAssistance');
Route::post('event/notify/{event}/client', 'EventController@notifyClient');
Route::get('event/{event}/copy', 'EventController@copy');
Route::get('event/{event}/admin/{user}', 'EventController@setAdmin');

Route::post('event/{event}/{time}/staff', 'EventController@assign');

Route::get('timesheets', 'EventController@timesheetIndex');
Route::get('event/{event}/timesheet', 'EventController@getTimesheet');
Route::post('event/{event}/timesheet', 'EventController@saveTimesheet');
Route::post('event/{event}/start-time-confirm', 'EventController@confirmStartTime');

Route::get('event/notify-all/{event}', 'AssignmentController@notifyAll');
Route::get('event/notify/{assignment}', 'AssignmentController@notify');
Route::get('event/{event}/confirm', 'AssignmentController@confirm');
Route::post('event/{event}/confirm', 'AssignmentController@forceConfirm');
Route::get('assignment/delete/{assignment}', 'AssignmentController@destroy');

Route::get('event/modifications/{eventId}', 'EventController@showModifications');
Route::get('event/back-up/{eventId}/{modificationId}', 'EventController@backUp');

	//Download link for files

	Route::get('event/download/{filename}', function($filename){
		$file = public_path().'/storage/events/'.$filename;
		if(file_exists($file))
		{
			return Response::download($file, $filename, ['Content-Length: '.filesize($file)]);
		}
		else
		{
			exit('This file does not exist any more');
		}
	});

// EVENT FEEDBACK

Route::get('feedback/{hash}', 'FeedbackController@form');
Route::get('feedback/request/{event}', 'FeedbackController@request');
Route::get('feedback/view/{event}', 'FeedbackController@show');

Route::post('feedback/{hash}', 'FeedbackController@submit');

// EVENT BAR

Route::resource('bar-event', 'BarEventController');
Route::get('bar-event/{eventBarId}', 'BarEventController@show');
Route::get('bar-event/create/{eventBarId}', 'BarEventController@edit');
Route::post('bar-event/create/{eventBarId}', 'BarEventController@store');
ROute::get('/bar-event/confirm/{eventBarId}', 'BarEventController@confirm');


// AVAILABILITES

Route::resource('availability', 'AvailabilityController');

Route::get('availability/create/dates', 'AvailabilityController@dates');

// CLIENTS

Route::resource('client', 'ClientController');

// REPORTS

Route::get('reports', function(){
	return view('reports.index');
});

Route::get('reports/month-report', 'EventController@briefMonthReport');
Route::get('reports/month-report/{year}/{month}/{order}', 'EventController@monthReport');

Route::get('reports/week-report/', 'AssignmentController@weekReport');
Route::get('reports/week-report/{week}', 'AssignmentController@WeekReport');

// PUBLIC HOLIDAYS

Route::resource('public-holidays', 'PublicHolidayController');
Route::get('public-holidays/create', 'PublicHolidayController@create');
Route::get('public-holidays/{publicHolidayId}/edit', 'PublicHolidayController@edit');
Route::get('public-holidays/{publicHolidayId}/delete', 'PublicHolidayController@destroy');

// UNIFORMS

Route::resource('uniforms', 'UniformController');
Route::get('uniforms/create', 'UniformController@create');
Route::get('uniforms/{uniformId}/edit', 'UniformController@edit');
Route::get('uniforms/{uniformId}/delete', 'UniformController@destroy');

//STOCKS

Route::resource('stocks', 'StockController');
Route::post('stocks/sort', 'StockController@sort');
Route::get('stocks/{id}/delete', 'StockController@destroy');

//OUTSTOCKS

Route::get('outstocks/{outstock}/destroy/{eventId}', 'OutStockController@destroy');


// Google calendar

Route::get('/oauth', 'GoogleCalendarController@oauth');
Route::get('/create_calendar_event/{hash}', 'GoogleCalendarController@create_calendar_event');

// MODIFICATIONS

Route::get('/modification/{{message}}', 'ModificationsController@create');
Route::get('/modification/{{eventId}}/get', 'ModificationsController@get');