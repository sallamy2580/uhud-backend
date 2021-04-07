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
Route::group([
  
  'middleware' => 'api',
  'prefix' => 'auth'

], function () {
  Route::post('login', 'Auth\AuthController@login');
  Route::post('register', 'Auth\AuthController@register');
  Route::get('logout', 'Auth\AuthController@logout');
  Route::get('refresh', 'Auth\AuthController@refresh');
  Route::get('me', 'Auth\AuthController@me');
  
  Route::get('send-forget-pass-email', 'Auth\AuthController@sendForgetPassEmail');
  Route::post('send-forget-pass-email', 'Auth\AuthController@sendForgetPassEmail');
  Route::post('change-password/{encryptUSerId}', 'Auth\AuthController@changePasswordFromForgetPass');
  
  Route::get('/send-email-verification-link', 'Auth\AuthController@sendEmailVerificationLink');
  Route::get('/verification-email/{emailVerifyToken}', 'Auth\AuthController@verificationEmail');

//    Route::get('/socialite/redirect/{provider}', 'Auth\AuthController@socialiteRedirect');
  Route::get('/socialite/callback/{provider}', 'Auth\AuthController@socialiteCallback');
  Route::post('/socialite/callback/{provider}', 'Auth\AuthController@socialiteCallback');
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'users'

], function () {
  Route::get('/all', 'User\UserController@all');
  Route::post('/add', 'User\UserController@add');
  Route::post('/update', 'User\UserController@update');
  Route::post('/change-password', 'User\UserController@changePassword');
  Route::get('/delete/{id}', 'User\UserController@delete');
  
  Route::group([
    'prefix' => 'tickets'
  ], function () {
    Route::get('/all', 'User\TicketController@all');
  });
  
  Route::group([
    'prefix' => 'notifications'
  ], function () {
    Route::get('/all', 'User\NotificationController@all');
    Route::get('/count', 'User\NotificationController@count');/*#swag*/
  });
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'tickets'

], function () {
  Route::get('/all', 'Ticket\TicketController@all');
  Route::post('/add', 'Ticket\TicketController@add');
  Route::post('/answer/{ticketId}', 'Ticket\TicketController@answer');
  Route::get('/close/{ticketId}', 'Ticket\TicketController@close');
  Route::get('/delete/{ticketId}', 'Ticket\TicketController@delete');
  Route::get('/sections/{ticketId}', 'Ticket\TicketController@ticketSections');
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'notifications'

], function () {
  Route::get('/all', 'Notification\NotificationController@all');
  Route::get('/count', 'Notification\NotificationController@count');/*#swag*/
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'packages'

], function () {
  Route::get('/all', 'Package\PackageController@all');
  Route::post('/add', 'Package\PackageController@add');
  Route::post('/update', 'Package\PackageController@update');
  Route::get('/delete/{id}', 'Package\PackageController@delete');
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'transactions'

], function () {
  Route::get('/all', 'Transaction\TransactionController@all');
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'hotels'

], function () {
  Route::get('/all', 'Hotels\HotelController@all');
  Route::post('/add', 'Hotels\HotelController@add');
  Route::post('/update', 'Hotels\HotelController@update');
  Route::get('/delete/{id}', 'Hotels\HotelController@delete');
  Route::get('/images/{hotelId}', 'Hotels\HotelController@hotelImages');/*#new*/
  Route::get('/rooms/{hotelId}', 'Hotels\HotelController@hotelRooms');/*#new*/
  Route::get('/rooms/reserves/{hotelId}', 'Hotels\HotelController@hotelRoomReserves');/*#new*/
  Route::get('/all-rooms', 'Hotels\HotelController@allRooms');
  Route::get('/all-reserves', 'Hotels\HotelController@allReserves');
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'airlines'

], function () {
  Route::get('/all', 'Airline\AirlineController@all');
  Route::post('/add', 'Airline\AirlineController@add');
  Route::post('/update', 'Airline\AirlineController@update');
  Route::get('/delete/{id}', 'Airline\AirlineController@delete');
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'flights'

], function () {
  Route::get('/all', 'Flight\FlightController@all');
  Route::post('/add', 'Flight\FlightController@add');
  Route::post('/update', 'Flight\FlightController@update');
  Route::get('/delete/{id}', 'Flight\FlightController@delete');
  Route::get('/seats/{flightId}', 'Flight\FlightController@seats');
  Route::get('/seats/reservs/{flightId}', 'Flight\FlightController@reservs');
  Route::get('/all-seats', 'Flight\FlightController@allSeats');
  Route::get('/all-reservs', 'Flight\FlightController@allReservs');
});

/*#new*/
Route::group([
  
  'middleware' => 'api',
  'prefix' => 'booking'

], function () {
  Route::get('/all', 'Booking\BookingController@all');
  Route::post('/add', 'Booking\BookingController@add');
  Route::post('/update', 'Booking\BookingController@update');
  Route::get('/delete/{id}', 'Booking\BookingController@delete');
});

/*#new*/
Route::group([
  
  'middleware' => 'api',
  'prefix' => 'admin'

], function () {
  Route::get('/dashboard', 'Admin\AdminController@dashboard');
  Route::group([
    'prefix' => 'contact'
  ], function () {
    Route::get('/all', 'Admin\AdminController@allContacts');
  });
  Route::group([
    'prefix' => 'user'
  ], function () {
    Route::post('/change-password', 'Admin\AdminController@changePassword');
  });
  
  
});

Route::group([
  
  'middleware' => 'api',
  'prefix' => 'pdf'

], function () {
  Route::get('/tst', 'PDF\PDFController@tst');
  Route::get('/booking/{id}', 'PDF\PDFController@bookingPdf');
});

Route::group([
  'prefix' => 'guest'
], function () {
  Route::group([
    'prefix' => 'contact'
  ], function () {
    Route::post('/add', 'Guest\GuestController@addNewContact');
  });
  Route::get('/countries', 'Guest\GuestController@countries');
  Route::get('/states', 'Guest\GuestController@states');
  Route::get('/cities', 'Guest\GuestController@cities');
});
