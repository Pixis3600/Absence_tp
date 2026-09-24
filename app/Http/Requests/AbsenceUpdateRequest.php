<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsenceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'date_debut' => ['required', 'date'],
            'date_fin' => [
                'required',
                'date',
                'after_or_equal:date_debut',
                function ($attribute, $value, $fail) {
                    if ($this->motif !== 'Congé payé') {
                        return;
                    }

                    $start = \Carbon\Carbon::parse($this->date_debut);
                    $end = \Carbon\Carbon::parse($value);

                    $days = $start->diffInDays($end) + 1;

                    $existingDays = \App\Models\Absence::where('user_id', $this->user_id)
                        ->where('motif', 'Congé payé')
                        ->where(function ($query) use ($start) {
                            $query->whereYear('date_debut', $start->year)
                                ->orWhereYear('date_fin', $start->year);
                        })
                        ->whereKeyNot($this->route('absence')?->id)
                        ->get()
                        ->sum(function ($absence) use ($start) {
                            $absenceStart = \Carbon\Carbon::parse($absence->date_debut);
                            $absenceEnd = \Carbon\Carbon::parse($absence->date_fin);

                            $yearStart = \Carbon\Carbon::create($start->year, 1, 1);
                            $yearEnd = \Carbon\Carbon::create($start->year, 12, 31);

                            $rangeStart = $absenceStart->max($yearStart);
                            $rangeEnd = $absenceEnd->min($yearEnd);

                            if ($rangeStart->greaterThan($rangeEnd)) {
                                return 0;
                            }

                            return $rangeStart->diffInDays($rangeEnd) + 1;
                        });

                    if ($existingDays + $days > 25) {
                        $fail('Un employé ne peut pas dépasser 25 jours de congé payé par an.');
                    }
                },
            ],
            'motif' => ['required', 'in:Accidents du travail,Congé payé,Congé paternité,Congé maternité,Congé maladie,Congé sans solde,Congé pour mariage,Congé pour décès,Formation,Autres'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Veuillez choisir un employé.',
            'user_id.exists' => 'Cet employé n’existe pas.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
            'motif.required' => 'Le motif est obligatoire.',
            'motif.in' => 'Le motif choisi est invalide.',
        ];
    }
}
