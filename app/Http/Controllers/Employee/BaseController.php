<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Models\Tenant\Employee;
use App\Models\Tenant\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public function profileView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('employee.pages.profile',
                     [
                         'employee' => Auth::guard('employee')->user(),
                     ]);
    }


    public function manageEventsView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $employee = Auth::guard('employee')->user();

        if (!$employee || !$employee->can('see-events')) {
            abort(403, 'Accès non autorisé à la liste des évènements.');
        }

        return view('employee.pages.manage-events',
                     [
                         'employee' => $employee,
                     ]);
    }

    public function manageTicketsView(Event $event , Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();

        if (!$user || !$user->can('see-tickets')) {
            abort(403, 'Accès non autorisé aux tickets.');
        }

        return view('employee.pages.manage-tickets', [
            'event' => $event,
        ]);
    }


}
