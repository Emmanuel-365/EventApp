<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
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
        return view('employee.auth.signup');
    }



    public function loginView(): Factory|View|Application
    {
        return view('employee.auth.login');
    }

    public function login(Request $request , BanService $banService): RedirectResponse
    {
        try {
            $employee = Employee::where('email',$request->input('email'));

            if (!$employee->exists()) {
                return back()->with('error', 'Adresse inconue.');
            }

            $employee = $employee->first();

            /**  if ($banService->isUserBanned($employee)) {
                return back()->with('error', 'Vous avez été bani .');
            }*/
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'authentification');
        }

        if ($employee && Hash::check($request->input('password'), $employee->password)) {
            Auth::guard('employee')->login($employee);
            $request->session()->regenerate();
            return redirect()->route('employee.profileView');
        } else {
            return back()->with(['error' => 'Mot de passe incorrect'], 401);
        }
    }


    public function logout(): RedirectResponse
    {
        Auth::guard('employee')->logout();
        return redirect()
            ->route('employee.auth.disconnected.login')
            ->with('success','Déconnexion réussie');
    }

}
