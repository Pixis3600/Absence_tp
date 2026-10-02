<?php

namespace App\Repositories;

use Silber\Bouncer\Database\Role;

class RoleRepository
{
    public function __construct(private readonly Role $role)
    {
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

        $abilityIds = array_values(array_unique(array_map('intval', $inputs['abilities'] ?? [])));

        $role->abilities()->sync($abilityIds);

        return $role;
    }
}
