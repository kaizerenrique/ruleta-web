<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->orderBy('name')->paginate(15);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name');
        $rolesDisponibles = ['admin' => 'Administrador', 'usuario' => 'Usuario'];

        return view('admin.usuarios.create', compact('roles', 'rolesDisponibles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::default()],
            'role'     => ['required', Rule::in(Role::pluck('name')->all())],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', "Usuario {$user->name} creado como {$data['role']}.");
    }

    public function edit(User $usuario)
    {
        $roles = Role::orderBy('name')->pluck('name');
        $rolesDisponibles = ['admin' => 'Administrador', 'usuario' => 'Usuario'];
        $rolActual = $usuario->getRoleNames()->first();

        return view('admin.usuarios.edit', compact('usuario', 'roles', 'rolesDisponibles', 'rolActual'));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'confirmed', Password::default()],
            'role'     => ['required', Rule::in(Role::pluck('name')->all())],
        ]);

        // Evitar que el último admin se auto-degrade
        if (
            $usuario->id === auth()->id()
            && $usuario->hasRole('admin')
            && $data['role'] !== 'admin'
        ) {
            return back()->with('error', 'No puedes quitarte a ti mismo el rol de administrador.');
        }

        if (
            $usuario->hasRole('admin')
            && $data['role'] !== 'admin'
            && User::role('admin')->count() <= 1
        ) {
            return back()->with('error', 'Debe existir al menos un administrador.');
        }

        $usuario->name  = $data['name'];
        $usuario->email = $data['email'];

        if (! empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();
        $usuario->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($usuario->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'Debe existir al menos un administrador.');
        }

        $usuario->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
