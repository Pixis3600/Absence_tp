<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsenceRequest;
use App\Mail\InfoMail;
use App\Models\Absence;
use App\Models\User;
use App\Repositories\AbsenceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        if ($status !== 'en_attente') {
            abort(403, 'Une absence déjà validée ou refusée ne peut plus être modifiée.');
        }

        if ($this->canEditAnyAbsence()) {
            return;
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

    public function store(AbsenceRequest $request)
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

    public function update(AbsenceRequest $request, Absence $absence)
    {
        $this->authorizeOwnAbsence($absence);

        $validated = $request->validated();

        if (!$this->canEditAnyAbsence()) {
            $validated['user_id'] = Auth::id();
        }

        $updatedAbsence = $this->repository->update($absence, $validated);
        $updatedAbsence->loadMissing('user');

        if ($updatedAbsence->user?->email) {
            Mail::to($updatedAbsence->user)->send(new InfoMail($updatedAbsence));
        }

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

    public function sendTestMail(Request $request)
    {
        if (!$this->canViewAllAbsences()) {
            abort(403, 'Seuls les administrateurs peuvent envoyer un mail de test.');
        }

        $validated = $request->validate([
            'absence_id' => ['required', 'exists:absences,id'],
            'email' => ['required', 'email'],
        ]);

        $absence = Absence::with('user')->findOrFail($validated['absence_id']);

        Mail::to($validated['email'])->send(new InfoMail($absence));

        return redirect()->route('absences.index')->with('success', 'Mail de test envoye avec succes.');
    }
}
