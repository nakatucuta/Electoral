<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleManagementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $search = trim((string) $request->string('q'));
        $role = trim((string) $request->string('role'));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('documento_identificacion', 'like', '%' . $search . '%')
                        ->orWhere('sede', 'like', '%' . $search . '%');
                });
            })
            ->when(in_array($role, ['admin', 'employee'], true), fn ($query) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('users.roles', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'roleOptions' => [
                'admin' => 'Administrador',
                'employee' => 'Empleado',
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $data = $request->validate([
            'role' => ['required', 'in:admin,employee'],
        ]);

        if ($user->role === 'admin' && $data['role'] === 'employee') {
            $adminsCount = User::query()->where('role', 'admin')->count();

            if ($adminsCount <= 1) {
                return back()->withErrors([
                    'role' => 'No se puede dejar la plataforma sin al menos un administrador.',
                ]);
            }
        }

        $user->update([
            'role' => $data['role'],
        ]);

        return back()->with('flash.banner', 'Rol actualizado correctamente.');
    }
}
