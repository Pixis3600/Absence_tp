<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsenceStoreRequest;
use App\Http\Requests\AbsenceUpdateRequest;
use App\Models\Absence;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    protected function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    protected function authorizeOwnAbsence(Absence $absence): void
    {
        $status = $absence->status ?? 'en_attente';

        if ($this->isAdmin()) {
            return;
        }

        if ($status !== 'en_attente') {
            abort(403, 'Une absence déjà validée ou refusée ne peut plus être modifiée par un employé.');
        }

        if ($absence->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres absences.');
        }
    }

    public function index()
    {
        $query = Absence::with('user')->latest();

        if (!$this->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $absences = $query->get();

        return view('absences.index', compact('absences'));
    }

    public function create()
    {
        $users = $this->isAdmin() ? User::orderBy('name')->get() : collect([Auth::user()]);

        return view('absences.create', compact('users'));
    }

    public function store(AbsenceStoreRequest $request)
    {
        $validated = $request->validated();

        if (!$this->isAdmin()) {
            $validated['user_id'] = Auth::id();
        }

        Absence::create($validated);

        return redirect()->route('absences.index')->with('success', 'Absence ajoutée avec succès.');
    }

    public function edit(Absence $absence)
    {
        $this->authorizeOwnAbsence($absence);

        $users = $this->isAdmin() ? User::orderBy('name')->get() : collect([Auth::user()]);

        return view('absences.edit', compact('absence', 'users'));
    }

    public function update(AbsenceUpdateRequest $request, Absence $absence)
    {
        $this->authorizeOwnAbsence($absence);

        $validated = $request->validated();

        if (!$this->isAdmin()) {
            $validated['user_id'] = Auth::id();
        }

        $absence->update($validated);

        return redirect()->route('absences.index')->with('success', 'Absence modifiée avec succès.');
    }

    public function destroy(Absence $absence)
    {
        $this->authorizeOwnAbsence($absence);

        $absence->delete();

        return redirect()->route('absences.index')->with('success', 'Absence supprimée.');
    }

    public function accept(Absence $absence)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent valider une absence.');
        }

        $absence->update(['status' => 'accepte']);

        return redirect()->route('absences.index')->with('success', 'L\'absence a été acceptée.');
    }

    public function reject(Absence $absence)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent refuser une absence.');
        }

        $absence->update(['status' => 'refuse']);

        return redirect()->route('absences.index')->with('success', 'L\'absence a été refusée.');
    }
}
