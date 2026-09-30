<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Repositories\RoleRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class RoleController extends Controller
{
    public function __construct(private readonly RoleRepository $repository)
    {
    }

    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->with('abilities')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $abilities = Ability::query()
            ->whereNull('entity_id')
            ->whereNull('entity_type')
            ->orderBy('name')
            ->get();

        return view('roles.create', compact('abilities'));
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->repository->store($validated);

        return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès.');
    }

    public function edit(Role $role): View
    {
        $role->load('abilities');

        $abilities = Ability::query()
            ->whereNull('entity_id')
            ->whereNull('entity_type')
            ->orderBy('name')
            ->get();

        return view('roles.edit', compact('role', 'abilities'));
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        $this->repository->update($role, $validated);

        return redirect()->route('roles.index')->with('success', 'Rôle modifié avec succès.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->repository->destroy($role);

        return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès.');
    }
}
