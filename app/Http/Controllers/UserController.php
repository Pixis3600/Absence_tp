<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Silber\Bouncer\Database\Role;

class UserController extends Controller
{
    public function __construct(private readonly UserRepository $repository)
    {
    }

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

        $this->repository->store($validated);

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

        $this->repository->update($user, $validated);

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
