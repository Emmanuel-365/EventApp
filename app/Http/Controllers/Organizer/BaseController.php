<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public function profileView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('organization.organizer.pages.profile',
                     [
                         'organizer' => Auth::guard('organizer')->user(),
                     ]);
    }

    public function organizationsView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('organization.organizer.pages.organizations',
                     [
                         'organizer' => Auth::guard('organizer')->user(),
                     ]);
    }


}
