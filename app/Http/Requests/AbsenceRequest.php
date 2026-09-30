<?php

namespace App\Http\Requests;

use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AbsenceRequest extends FormRequest
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

                    $start = Carbon::parse($this->date_debut);
                    $end = Carbon::parse($value);

                    $days = $start->diffInDays($end) + 1;

                    $existingDays = Absence::where('user_id', $this->user_id)
                        ->where('motif', 'Congé payé')
                        ->where(function ($query) use ($start) {
                            $query->whereYear('date_debut', $start->year)
                                ->orWhereYear('date_fin', $start->year);
                        })
                        ->when($this->isMethod('put') || $this->isMethod('patch'), function ($query) {
                            $query->whereKeyNot($this->route('absence')?->id);
                        })
                        ->get()
                        ->sum(function ($absence) use ($start) {
                            $absenceStart = Carbon::parse($absence->date_debut);
                            $absenceEnd = Carbon::parse($absence->date_fin);

                            $yearStart = Carbon::create($start->year, 1, 1);
                            $yearEnd = Carbon::create($start->year, 12, 31);

                            $rangeStart = $absenceStart->max($yearStart);
                            $rangeEnd = $absenceEnd->min($yearEnd);

                            if ($rangeStart->greaterThan($rangeEnd)) {
                                return 0;
                            }

                            return $rangeStart->diffInDays($rangeEnd) + 1;
                        });

                    if ($existingDays + $days > 25) {
                        $fail(__('Un employé ne peut pas dépasser 25 jours de congé payé par an.'));
                    }
                },
            ],
            'motif' => ['required', 'in:Accidents du travail,Congé payé,Congé paternité,Congé maternité,Congé maladie,Congé sans solde,Congé pour mariage,Congé pour décès,Formation,Autres'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('Veuillez choisir un employé.'),
            'user_id.exists' => __('Cet employé n’existe pas.'),
            'date_debut.required' => __('La date de début est obligatoire.'),
            'date_debut.date' => __('La date de début doit être une date valide.'),
            'date_fin.required' => __('La date de fin est obligatoire.'),
            'date_fin.date' => __('La date de fin doit être une date valide.'),
            'date_fin.after_or_equal' => __('La date de fin doit être égale ou postérieure à la date de début.'),
            'motif.required' => __('Le motif est obligatoire.'),
            'motif.in' => __('Le motif choisi est invalide.'),
        ];
    }
}
