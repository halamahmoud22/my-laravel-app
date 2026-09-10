<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CartController extends Controller
{
    use AuthorizesRequests;
    public function add(Request $request, Product $product)
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            $this->authorize('update', $cart);
            $cart->update(['quantity' => $cart->quantity + 1]);
        } else {
            $this->authorize('create', Cart::class);
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }
        return redirect()->route('cart.index');
    }

    public function update(Request $request, Cart $cart)
    {
        $this->authorize('update', $cart);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cart->update([
            'quantity' => $request->quantity,
        ]);
        return redirect()->route('cart.index');
    }

    public function index()
    {
        $this->authorize('viewAny', Cart::class);

        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function remove(Cart $cart)
    {
        $this->authorize('delete', $cart);
        $cart->delete();
        return redirect()->route('cart.index');
    }
}
