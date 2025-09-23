<?php

use App\Http\Controllers\Dashboard\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/dashboard', 'as' => 'dashboard.'], function () {

    ############################################# AUTHENTICATED ROUTES #############################################
    Route::middleware('auth')->group(function () {

        ############################################# CHATS ROUTES #############################################
        Route::controller(ChatController::class)->prefix('chat')->name('chat.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/start', 'startChat')->name('start');
            Route::get('/history', 'getChatHistory')->name('history');
            Route::get('/{chat}/messages', 'getMessages')->name('messages');
            Route::post('/{chat}/send', 'sendMessage')->name('send');
            Route::delete('/{chat}', 'deleteChat')->name('delete');
        });

    });

    require __DIR__ . '/auth.php';
});
