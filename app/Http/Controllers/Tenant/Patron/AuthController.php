<?php

namespace App\Http\Controllers\Tenant\Patron;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant\Patron;

class AuthController extends Controller
{
    public function loginView(): Factory|View|Application
    {
        return view('organization.patron.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {

        $patron = Patron::where('email', $request->input('email'))->first();
        if ($patron && Hash::check($request->input('password'), $patron->password)) {
            Auth::guard('patron')->login($patron);
            return redirect()->route('patron.patronPanel');
        } else {

            return back()->withErrors([
                'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.',
            ])->withInput($request->only('email'));
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('patron')->logout();
        return redirect()
            ->route('patron.login')
            ->with('success', 'Déconnexion réussie');
    }
}
