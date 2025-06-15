<?php

namespace App\Http\Controllers\Tenant\Patron;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{


    public function patronPanel(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('organization.patron.pages.manage-employees',
                     [
                         'patron' => Auth::guard('patron')->user(),
                     ]);
    }


}
