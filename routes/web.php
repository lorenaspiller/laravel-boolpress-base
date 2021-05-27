<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'BlogController@index')->name('guest.posts.index');
// rotta della show
Route::get('posts/{slug}', 'BlogController@show')->name('guest.posts.show');
//rotta per la view che mi da i post filtrati per tag
Route::get('tags/{slug}', 'BlogController@filterTag')->name('guest.posts.filter-tag');
// rotta per postare commenti
Route::post('posts/{post}/add-comment', 'BlogController@addComment')->name('guest.posts.add-comment');


Route::prefix('admin')->name('admin.')->namespace('Admin')->group(function () {
    // rotte all'interno di questo gruppo admin
    Route::resource('posts', 'PostController');
    Route::delete('comments/{comment}', 'CommentController@destroy')->name('comments.destroy');
});

