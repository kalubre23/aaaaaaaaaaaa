<?php

use App\Http\Controllers\MarkController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/students/{subject_id}', [UserController::class, 'listStudents'])->name('users.listStudents');


    Route::apiResource('/subjects', SubjectController::class);
    Route::post('/subjects/{subject}/add', [SubjectController::class, 'add_students'])->name('subjects.add_students');
    Route::post('/subjects/{subject}/remove', [SubjectController::class, 'remove_students'])->name('subjects.remove_students');

    Route::get('/marks', [MarkController::class, 'marksList'])->name('marks.marksList');
    Route::get('/marks/{subject}/{student?}', [MarkController::class, 'marks'])->name('marks.marks');
    Route::post('/marks/{subject}/{student}', [MarkController::class, 'store'])->name('marks.store');
    Route::put('/marks/{mark}', [MarkController::class, 'update'])->name('marks.update');
    Route::delete('/marks/{mark}', [MarkController::class, 'destroy'])->name('marks.destroy');


});
