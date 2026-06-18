<?php

namespace App\Livewire\Frontend;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class CustomerSetup extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $token;

    public function mount($token): void
    {
        $customer = Customer::where('setup_token', $token)->firstOrFail();

        $this->name = $customer->full_name;
        $this->email = $customer->email;
        $this->token = $token;
    }

    public function register(): void
    {
        $customer = Customer::where('setup_token', $this->token)->firstOrFail();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $customer->setup_token = null;
        $customer->user_id = $user->id;
        $customer->save();

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.customer-setup');
    }
}
