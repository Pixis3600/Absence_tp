<?php

namespace App\Repositories;

use App\Models\User;
use Silber\Bouncer\BouncerFacade as Bouncer;

class UserRepository
{
    public function __construct(private readonly User $user)
    {
    }

    public function store(array $inputs): User
    {
        $user = new $this->user;

        return $this->save($user, $inputs);
    }

    public function update(User $user, array $inputs): User
    {
        return $this->save($user, $inputs);
    }

    private function save(User $user, array $inputs): User
    {
        $user->name = $inputs['name'];
        $user->lastname = $inputs['lastname'];
        $user->email = $inputs['email'];

        if (!empty($inputs['password'])) {
            $user->password = bcrypt($inputs['password']);
        }

        $user->save();

        if (!empty($inputs['role'])) {
            Bouncer::retract($user->getRoles())->from($user);
            Bouncer::assign($inputs['role'])->to($user);
            Bouncer::refresh($user);
        }

        return $user;
    }
}
