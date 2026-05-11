<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\SegmentController::class, 'page']);

Route::get('/api/segments', [App\Http\Controllers\SegmentController::class, 'index']);
