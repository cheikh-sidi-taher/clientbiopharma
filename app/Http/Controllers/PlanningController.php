<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanningController extends Controller
{
    private const OBJECTIVE_MIN = 5;
    private const OBJECTIVE_MAX = 10;

    public function index(Request $request)
    {
        $user = Auth::user();

        $selectedDateRaw = $request->get('date');
        try {
            $selectedDate = $selectedDateRaw ? Carbon::parse($selectedDateRaw) : now();
        } catch (\Throwable $e) {
            $selectedDate = now();
        }

        $weekStart = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6);

        $visitsInWeek = Visit::with(['pharmacy.zone'])
            ->where('agent_id', $user->id)
            ->whereBetween('scheduled_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();

        $visitsByDate = $visitsInWeek->groupBy(fn (Visit $v) => $v->scheduled_date->toDateString());

        $plannedIdsMapByDate = [];
        foreach ($visitsByDate as $dayKey => $dayVisits) {
            $plannedIdsMapByDate[$dayKey] = array_fill_keys(
                $dayVisits->pluck('pharmacy_id')->all(),
                true
            );
        }

        $pharmacies = Pharmacy::with('zone')
            ->whereHas('zone', fn ($q) => $q->where('status', 'active'))
            ->orderBy('name')
            ->get();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $dayDate = $weekStart->copy()->addDays($i);
            $dayKey = $dayDate->toDateString();

            $dayVisits = $visitsByDate->get($dayKey, collect())->sortBy(
                fn ($v) => $v->pharmacy?->name ?? ''
            );

            $days[] = [
                'date' => $dayKey,
                'label' => $dayDate->locale('fr')->isoFormat('dddd'),
                'display' => $dayDate->format('d/m'),
                'visits' => $dayVisits,
                'plannedCount' => $dayVisits->count(),
            ];
        }

        return view('planning.index', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'days' => $days,
            'pharmacies' => $pharmacies,
            'objectiveMin' => self::OBJECTIVE_MIN,
            'objectiveMax' => self::OBJECTIVE_MAX,
            'plannedIdsMapByDate' => $plannedIdsMapByDate,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'scheduled_date' => 'required|date',
        ]);

        $scheduledDate = Carbon::parse($validated['scheduled_date'])->toDateString();
        $pharmacyId = (int) $validated['pharmacy_id'];

        $duplicate = Visit::where('agent_id', $user->id)
            ->where('pharmacy_id', $pharmacyId)
            ->whereDate('scheduled_date', $scheduledDate)
            ->exists();

        if ($duplicate) {
            return redirect()->route('planning.index', ['date' => $scheduledDate])
                ->withErrors(['duplicate' => 'Cette pharmacie est déjà planifiée pour cette date.'])
                ->withInput();
        }

        Visit::create([
            'pharmacy_id' => $pharmacyId,
            'agent_id' => $user->id,
            'scheduled_date' => $scheduledDate,
            'status' => 'planifié',
            'created_by' => $user->id,
        ]);

        return redirect()->route('planning.index', ['date' => $scheduledDate])
            ->with('success', 'Visite planifiée.');
    }

    public function destroy(Visit $visit)
    {
        $user = Auth::user();

        if (!$user->hasRole('Admin') && $visit->agent_id !== $user->id) {
            abort(403);
        }

        $scheduledDate = $visit->scheduled_date->toDateString();
        $visit->delete();

        return redirect()->route('planning.index', ['date' => $scheduledDate])
            ->with('success', 'Visite supprimée.');
    }
}

