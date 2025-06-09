<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public function profileView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('admin.pages.profile',
                     [
                         'admin' => Auth::guard('admin')->user(),
                     ]);
    }

    public function manageOrganizerView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || !$admin->can('see-organizer-profile')) {
            abort(403, 'Accès non autorisé à la liste des organisateurss.');
        }
        return view('admin.pages.manage-organizers',
                     [
                         'admin' => $admin,
                     ]);
    }

    public function manageOrganizerOrganizationsView(Organizer $organizer): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || !$admin->can('see-organization')) {
            abort(403, 'Accès non autorisé à la liste des organisations.');
        }

        return view('admin.pages.manage-organizer-organizations',
                     [
                         'admin' => $admin,
                         'organizer' => $organizer,
                     ]);
    }

    public function manageOrganizationsView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || !$admin->can('see-organization')) {
            abort(403, 'Accès non autorisé à la liste des organisations.');
        }

        return view('admin.pages.manage-organizations',
                     [
                         'admin' => $admin,
                     ]);
    }


}
