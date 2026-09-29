<?php

namespace App\Repositories;

use App\Models\Absence;

class AbsenceRepository
{
    public function __construct(private readonly Absence $absence)
    {
    }

    public function store(array $inputs): Absence
    {
        $absence = new $this->absence;

        return $this->save($absence, $inputs);
    }

    public function update(Absence $absence, array $inputs): Absence
    {
        return $this->save($absence, $inputs);
    }

    public function delete(Absence $absence): void
    {
        $absence->delete();
    }

    public function setStatus(Absence $absence, string $status): Absence
    {
        $absence->status = $status;
        $absence->save();

        return $absence;
    }

    private function save(Absence $absence, array $inputs): Absence
    {
        $absence->date_debut = $inputs['date_debut'];
        $absence->date_fin = $inputs['date_fin'];
        $absence->motif = $inputs['motif'];
        $absence->user_id = $inputs['user_id'];

        if (isset($inputs['status'])) {
            $absence->status = $inputs['status'];
        }

        $absence->save();

        return $absence;
    }
}
