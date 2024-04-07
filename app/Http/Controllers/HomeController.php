<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function getRestaurantsByMeal($meal)
    {
        if (empty($meal)) {
            return response()->json(['error' => 'The meal is required.'], 422);
        }

        $jsonContents = File::get(public_path('data/dishes.json'));
        $dishes = json_decode($jsonContents, true)['dishes'];

        $dishesFilterByMeal = array_filter($dishes, function ($dish) use ($meal) {
            return in_array($meal, $dish['availableMeals']);
        });

        $restaurants = array_unique(array_column($dishesFilterByMeal, 'restaurant'));

        return response()->json([$restaurants]);
    }

    public function getDishesByMealAndRestaurant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meal' => 'required|string',
            'restaurant' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jsonContents = File::get(public_path('data/dishes.json'));
        $dishes = json_decode($jsonContents, true)['dishes'];

        $meal = $request->meal;
        $restaurant = $request->restaurant;

        $dishesFilterByMealAndRestaurant = array_filter($dishes, function ($dish) use ($meal, $restaurant) {
            return in_array($meal, $dish['availableMeals']) && ($restaurant === $dish['restaurant']);
        });

        $dishesName = array_unique(array_column($dishesFilterByMealAndRestaurant, 'name'));

        return response()->json([$dishesName]);
    }
}
