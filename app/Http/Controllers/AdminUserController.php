<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    private const PERMISSION_KEYS = [
        'bookings', 'contacts',
        'cms', 'vehicles', 'tours', 'zones', 'carousel', 'gallery', 'sections',
        'settings',
    ];

    public function __construct(private AdminUserService $users) {}

    public function index()
    {
        $this->requireAdmin();
        $users = $this->users->listAllOrdered();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role' => 'required|in:admin,editor',
        ]);

        try {
            $this->users->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'permissions' => $this->extractPermissions($request),
                'is_active' => true,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el usuario. Inténtalo de nuevo.');
        }

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        $this->requireAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes editar tu propio usuario desde aquí.');
        }

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'unique:users,email,'.$user->id],
            'role' => 'required|in:admin,editor',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'permissions' => $this->extractPermissions($request),
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => Password::min(8)]);
            $data['password'] = $request->password;
        }

        try {
            $this->users->update($user, $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el usuario. Inténtalo de nuevo.');
        }

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $this->requireAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        try {
            $this->users->delete($user);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el usuario. Inténtalo de nuevo.');
        }

        return back()->with('success', 'Usuario eliminado.');
    }

    public function toggle(User $user)
    {
        $this->requireAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes desactivarte a ti mismo.');
        }

        $this->users->toggleActive($user);

        return back()->with('success', 'Estado del usuario actualizado.');
    }

    // ── Helpers ────────────────────────────────────────────────

    private function requireAdmin(): void
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    private function extractPermissions(Request $request): array
    {
        $perms = [];
        foreach (self::PERMISSION_KEYS as $key) {
            $perms[$key] = $request->boolean('perm_'.$key);
        }

        return $perms;
    }
}
