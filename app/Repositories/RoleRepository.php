<?php

namespace App\Repositories;

use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class RoleRepository
{
    public function __construct(
        private readonly Role $role,
        private readonly Ability $ability,
    ) {
    }

    public function store(array $inputs): Role
    {
        $role = new $this->role;

        return $this->save($role, $inputs);
    }

    public function update(Role $role, array $inputs): Role
    {
        return $this->save($role, $inputs);
    }

    public function destroy(Role $role): void
    {
        $role->users()->detach();
        $role->abilities()->detach();
        $role->delete();
    }

    private function save(Role $role, array $inputs): Role
    {
        $role->name = $inputs['name'];
        $role->save();

        $abilityIds = $this->resolveAbilityIds(
            $inputs['abilities'] ?? [],
            $inputs['new_abilities'] ?? null,
        );

        $role->abilities()->sync($abilityIds);

        return $role;
    }

    /**
     * @param  int[]  $selectedAbilityIds
     * @return int[]
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
                $ability = $this->ability->query()->firstOrCreate([
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
