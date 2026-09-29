<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Silber\Bouncer\Database\Role;
use Silber\Bouncer\BouncerFacade as Bouncer;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create', [
            'roles' => $this->availableRoles(),
        ]);
    }

    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Bouncer::assign($validated['role'])->to($user);
        Bouncer::refresh($user);

        return redirect()->route('users.index')->with('success', 'Employé ajouté avec succès.');
    }

    public function edit(User $user)
    {
        $user->load('roles');

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->availableRoles(),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->lastname = $validated['lastname'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        Bouncer::retract($user->getRoles())->from($user);
        Bouncer::assign($validated['role'])->to($user);
        Bouncer::refresh($user);

        return redirect()->route('users.index')->with('success', 'Employé modifié avec succès.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Employé supprimé.');
    }

    public function show(User $user)
    {
        $user->load(['absences', 'roles']);

        return view('users.show', compact('user'));
    }

    private function availableRoles(): array
    {
        return Role::query()->orderBy('name')->pluck('name')->all();
    }
}
