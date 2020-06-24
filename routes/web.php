 <?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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


Auth::routes();
// Route::get('/scan', function(){
//     return view('scan');
// });

Route::get('/home', 'HomeController@index')->name('home');




Route::get('/api/users', 'api\UserController@index');

Route::get('/', 'ThreadController@index')->name('threads');

Route::get('/threads', 'ThreadController@index')->name('threads');
Route::get('/threads/new', 'ThreadController@create');
// Route::get('threads/search', 'SearchController@show');
Route::get('/threads/search', 'SearchController@show');

Route::get('/threads/{channel}', 'ThreadController@index');

Route::post('locked-threads/{thread}', 'LockedThreadsController@store')->name('locked-threads.store')->middleware('admin');
Route::delete('locked-threads/{thread}', 'LockedThreadsController@destroy')->name('locked-threads.destroy')->middleware('admin');

Route::get('/threads/{channel}/{thread}', 'ThreadController@show');
Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy');
Route::patch('/threads/{channel}/{thread}', 'ThreadController@update');


Route::get('/threads/{channel}/{thread}/replies', 'ReplyController@index');
Route::post('/threads/{channel}/{thread}/replies', 'ReplyController@store');
Route::patch('/replies/{reply}', 'ReplyController@update');
Route::delete('/replies/{reply}', 'ReplyController@destroy')->name('replies.destroy');

Route::post('/threads', 'ThreadController@store')->middleware('must-be-confirmed'); // custom registered middleware in kernel/php
Route::get('/register/confirm', 'Auth\RegisterConfirmationController@confirm')->name('register.confirm');// i made a sub controller


Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')->middleware('auth');


Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy');
Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy');


Route::post('/api/users/{user}/avatar', 'api\UserAvatarController@store')->middleware('auth')->name('avatar');
Route::post('/api/users/{user}/attachments', 'api\AttachmentsController@store')->middleware('auth');
Route::delete('/api/attachments/{attachments}', 'api\AttachmentsController@destroy')->middleware('auth');

               

