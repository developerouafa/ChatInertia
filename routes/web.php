<?php

use App\Http\Controllers\ChatpublicController;
use App\Http\Controllers\ChatPrivateController;
use App\Http\Controllers\ConversationUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });

    Route::group(['middleware' => ['setLocale']], function(){
        Route::get('/language/{language}', function (string $language) {
            session(['locale' => $language]);
            return redirect()->back();
        })->name('language');

                Route::get('/dashboard', function () {
                    return Inertia::render('Dashboard');
                })->middleware(['auth', 'verified'])->name('dashboard');

                Route::middleware('auth')->group(function () {
                    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
                    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
                    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


                        Route::get('/users', [UserController::class, 'index'])->middleware( ['permission:view-users', 'role:user'])
                                                                        ->name('users.index');


                        Route::get('/chat', [ChatpublicController::class, 'index'])->name('chat.index');
                        Route::post('/chat/send', [ChatpublicController::class, 'send'])->name('chat.send');

                        Route::get('/conversation', [ChatPrivateController::class, 'index'])->name('chat.conversation');
                        Route::get('/chat/open/{other_user}', [ChatPrivateController::class, 'openOrCreate'])->name('chat.open');
                        Route::post('/privatechat/send', [ChatPrivateController::class, 'send']);


                        Route::get('/conversations', [ConversationUserController::class, 'index'])
                        ->name('chat.list');

                        Route::get('/privatechat/conversation/{conversation}', [ConversationUserController::class, 'show'])->name('chat.show');

                        Route::post('/chatprivate/send', [ConversationUserController::class, 'store']);
                        Route::post('/chatprivate/mark-as-read', [ConversationUserController::class, 'markAsRead']);

                        Route::put('/chatprivate/message/{message}', [ConversationUserController::class, 'updateMessage']);

                        Route::delete('/chatprivate/conversation/{conversation}', [ConversationUserController::class, 'deleteConversation']);

                });

        });
require __DIR__.'/auth.php';
