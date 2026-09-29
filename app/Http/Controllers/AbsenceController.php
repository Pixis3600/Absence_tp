<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsenceStoreRequest;
use App\Http\Requests\AbsenceUpdateRequest;
use App\Models\Absence;
use App\Models\User;
use App\Repositories\AbsenceRepository;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    public function __construct(private readonly AbsenceRepository $repository)
    {
    }

    protected function canViewAllAbsences(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->can('absence-view-all') ?? false;
    }

    protected function canEditAnyAbsence(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->can('absence-edit-all') ?? false;
    }

    protected function canDeleteAnyAbsence(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->can('absence-delete-all') ?? false;
    }

    protected function canReviewAbsence(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->can('absence-review') ?? false;
    }

    protected function authorizeOwnAbsence(Absence $absence): void
    {
        $status = $absence->status ?? 'en_attente';

        if ($this->canEditAnyAbsence()) {
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

        if (!$this->canViewAllAbsences()) {
            $query->where('user_id', Auth::id());
        }

        $absences = $query->get();

        return view('absences.index', compact('absences'));
    }

    public function create()
    {
        $users = $this->canViewAllAbsences() ? User::orderBy('name')->get() : collect([Auth::user()]);

        return view('absences.create', compact('users'));
    }

    public function store(AbsenceStoreRequest $request)
    {
        $validated = $request->validated();

        if (!$this->canViewAllAbsences()) {
            $validated['user_id'] = Auth::id();
        }

        $this->repository->store($validated);

        return redirect()->route('absences.index')->with('success', 'Absence ajoutée avec succès.');
    }

    public function edit(Absence $absence)
    {
        $this->authorizeOwnAbsence($absence);

        $users = $this->canViewAllAbsences() ? User::orderBy('name')->get() : collect([Auth::user()]);

        return view('absences.edit', compact('absence', 'users'));
    }

    public function update(AbsenceUpdateRequest $request, Absence $absence)
    {
        $this->authorizeOwnAbsence($absence);

        $validated = $request->validated();

        if (!$this->canEditAnyAbsence()) {
            $validated['user_id'] = Auth::id();
        }

        $this->repository->update($absence, $validated);

        return redirect()->route('absences.index')->with('success', 'Absence modifiée avec succès.');
    }

    public function destroy(Absence $absence)
    {
        if (!$this->canDeleteAnyAbsence()) {
            $this->authorizeOwnAbsence($absence);
        }

        $this->repository->delete($absence);

        return redirect()->route('absences.index')->with('success', 'Absence supprimée.');
    }

    public function accept(Absence $absence)
    {
        if (!$this->canReviewAbsence()) {
            abort(403, 'Seuls les administrateurs peuvent valider une absence.');
        }

        $this->repository->setStatus($absence, 'accepte');

        return redirect()->route('absences.index')->with('success', 'L\'absence a été acceptée.');
    }

    public function reject(Absence $absence)
    {
        if (!$this->canReviewAbsence()) {
            abort(403, 'Seuls les administrateurs peuvent refuser une absence.');
        }

        $this->repository->setStatus($absence, 'refuse');

        return redirect()->route('absences.index')->with('success', 'L\'absence a été refusée.');
    }
}
