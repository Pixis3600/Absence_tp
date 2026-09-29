<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class RoleController extends Controller
{
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

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::query()->create([
            'name' => $validated['name'],
        ]);

        $abilityIds = $this->resolveAbilityIds(
            $validated['abilities'] ?? [],
            $validated['new_abilities'] ?? null
        );

        $role->abilities()->sync($abilityIds);

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

    public function update(RoleUpdateRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        $role->name = $validated['name'];
        $role->save();

        $abilityIds = $this->resolveAbilityIds(
            $validated['abilities'] ?? [],
            $validated['new_abilities'] ?? null
        );

        $role->abilities()->sync($abilityIds);

        return redirect()->route('roles.index')->with('success', 'Rôle modifié avec succès.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->users()->detach();
        $role->abilities()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès.');
    }

    /**
     * @param  int[]  $selectedAbilityIds
     */
    private function resolveAbilityIds(array $selectedAbilityIds, ?string $newAbilities): array
    {
        $abilityIds = $selectedAbilityIds;

        if ($newAbilities) {
            $names = collect(explode(',', $newAbilities))
                ->map(fn ($ability) => trim($ability))
                ->filter()
                ->unique();

            foreach ($names as $name) {
                $ability = Ability::query()->firstOrCreate([
                    'name' => $name,
                    'entity_id' => null,
                    'entity_type' => null,
                ], [
                    'title' => $name,
                    'only_owned' => false,
                    'options' => null,
                    'scope' => null,
                ]);

                $abilityIds[] = $ability->id;
            }
        }

        return array_values(array_unique(array_map('intval', $abilityIds)));
    }
}
