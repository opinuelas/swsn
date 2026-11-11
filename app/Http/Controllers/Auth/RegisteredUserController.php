<?php

namespace App\Http\Controllers\Auth;

use App\Gender;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'userid' => ['required', 'string', 'max:15', 'alpha_dash:ascii', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cell_number' => ['required', 'string', 'max:20'],
            'gender' => ['required', new Enum(Gender::class)],
        ], [
            'userid.unique' => __('This User ID is already taken. Please choose another one.'),
        ]);

        $user = User::create([
            'name' => $request->name,
            'userid' => $request->userid,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cell_number' => $request->cell_number,
            'gender' => $request->gender,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
