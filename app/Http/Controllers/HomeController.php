<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(12);

        // Branch: logged-in users see home (Metronic layout), guests see welcome
        if (auth()->check()) {
            return view('home', compact('products'));
        }

        return view('welcome', compact('products'));
    }
}
