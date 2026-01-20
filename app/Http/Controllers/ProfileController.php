<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

public function edit(Request $request): View
{
    $user = $request->user();
    $weather = null;

    // Solo pedimos el clima si el usuario tiene coordenadas guardadas
    // Eliminamos la llamada que tenías fuera del IF
    if ($user->lat && $user->lng) {
        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $user->lat,
            'lon' => $user->lng,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric',
            'lang' => 'es'
        ]);

        if ($response->successful()) {
            $weather = $response->json();
        }
    }

    return view('profile.edit', [
        'user' => $user,
        'weather' => $weather,
    ]);
}

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
