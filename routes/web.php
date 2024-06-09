<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PuzzlerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Route to submit the form to add participants
Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');

// Validate the word and record submission
Route::post('/puzzler/validate-word', [PuzzlerController::class, 'checkWord'])->name('puzzler.validateWord');

// End game 
Route::post('/end-game', [PuzzlerController::class, 'endGame'])->name('puzzler.endGame');



