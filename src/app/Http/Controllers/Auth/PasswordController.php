<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $rules = [
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];
        // password_setフラグで判定
        if ($user->password_set) {
            $rules['current_password'] = ['required', 'current_password'];
        }
        $validated = $request->validateWithBag('updatePassword', $rules);

        $user->update([
            'password' => Hash::make($validated['password']),
            'password_set' => true,
        ]);

        return back()->with('status', 'password-updated');
    }
}
