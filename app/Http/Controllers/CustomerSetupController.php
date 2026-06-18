<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class CustomerSetupController extends Controller
{
    public function showForm($token)
    {
        $customer = Customer::where('setup_token', $token)
            ->where('setup_token_expires_at', '>', Carbon::now())
            ->firstOrFail();

        return view('auth.customer_setup', compact('customer', 'token'));
    }

    public function store(Request $request, $token)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Customer::where('setup_token', $token)
            ->where('setup_token_expires_at', '>', Carbon::now())
            ->firstOrFail();

        $customer->password = Hash::make($request->password);
        $customer->setup_token = null;
        $customer->setup_token_expires_at = null;
        $customer->email_verified_at = now();
        $customer->save();

        return redirect()->route('login')->with('success', 'Account created successfully. Please log in.');
    }
}
