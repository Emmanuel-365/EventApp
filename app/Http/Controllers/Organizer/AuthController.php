<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Services\BanService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{


    public function signupView(): Factory|View|Application
    {
        return view('organization.organizer.auth.signup');
    }


    public function loginView(): Factory|View|Application
    {
        return view('organization.organizer.auth.login');
    }

    public function login(Request $request , BanService $banService): RedirectResponse
    {
        try {
            $organizer = Organizer::where('email',$request->input('email'));
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'authentification');
        }

        if (!$organizer->exists()) {
            return back()->with('error', 'Adresse e-mail ou mot de passe incorrect');
        }

        $organizer = $organizer->first();

        if ($banService->isUserBanned($organizer)){
            return back()->with('error', 'Vous avez été bani .');
        }


        if ($organizer && Hash::check($request->input('password'), $organizer->password)) {
            Auth::guard('organizer')->login($organizer);
            return  redirect()->route('organization.organizer.profileView');
        } else {
            return back()->with(['error' => 'Mot de passe incorrect'], 401);
        }
    }


    public function logout(): RedirectResponse
    {
        Auth::guard('organizer')->logout();
        return redirect()
            ->route('organization.organizer.auth.disconnected.login')
            ->with('success','Déconnexion réussie');
    }

}
