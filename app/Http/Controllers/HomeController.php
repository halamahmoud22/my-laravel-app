<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $products = Cache::remember("home_products_page_{$page}", now()->addMinutes(15), function () {
            return Product::with('category')->latest()->paginate(12);
        });

        // Branch: logged-in users see home (Metronic layout), guests see welcome
        if (auth()->check()) {
            return view('home', compact('products'));
        }

        return view('welcome', compact('products'));
    }

    public function toggleTheme(Request $request)
    {
        $currentTheme = $request->cookie('theme', 'light');
        $newTheme = $currentTheme === 'dark' ? 'light' : 'dark';

        // Update the user's theme preference if logged in
        if (auth()->check()) {
            auth()->user()->update(['theme' => $newTheme]);
        }

        // Set the theme cookie for guests
        return redirect()->back()->cookie('theme', $newTheme, 60 * 24 * 365);
    }
}
