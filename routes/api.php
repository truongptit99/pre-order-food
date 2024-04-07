<?php

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/restaurants/{meal}', [HomeController::class, 'getRestaurantsByMeal'])->name('restaurants.list');
Route::get('/dishes', [HomeController::class, 'getDishesByMealAndRestaurant'])->name('dishes.list');
