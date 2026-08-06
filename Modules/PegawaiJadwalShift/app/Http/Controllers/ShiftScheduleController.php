<?php

namespace Modules\PegawaiJadwalShift\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PegawaiJadwalShift\Http\Requests\StoreShiftScheduleRequest;
use Modules\PegawaiJadwalShift\Http\Requests\UpdateShiftScheduleRequest;
use Modules\PegawaiJadwalShift\Models\ShiftSchedule;
use Modules\PegawaiJadwalShift\Services\ShiftScheduleService;

class ShiftScheduleController extends Controller
{
    public function __construct(protected ShiftScheduleService $shiftScheduleService)
    {
    }

    public function index(Request $request)
    {
        $query = ShiftSchedule::query();

        if ($request->filled('shift_date')) {
            $query->whereDate('shift_date', $request->date('shift_date'));
        }

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        return $query->orderBy('shift_date')->orderBy('shift_type')->paginate(15);
    }

    /**
     * Lihat siapa jaga: filter wajib ward_id + rentang tanggal (from/to).
     */
    public function byWardAndDateRange(Request $request)
    {
        $data = $request->validate([
            'ward_id' => ['required', 'integer', 'exists:wards,id'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        return ShiftSchedule::query()
            ->where('ward_id', $data['ward_id'])
            ->whereBetween('shift_date', [$data['from'], $data['to']])
            ->orderBy('shift_date')
            ->orderBy('shift_type')
            ->get();
    }

    public function store(StoreShiftScheduleRequest $request)
    {
        $shiftSchedule = $this->shiftScheduleService->createSchedule($request->validated());

        return response()->json($shiftSchedule->refresh(), 201);
    }

    public function show(ShiftSchedule $shiftSchedule): ShiftSchedule
    {
        return $shiftSchedule;
    }

    public function update(UpdateShiftScheduleRequest $request, ShiftSchedule $shiftSchedule): ShiftSchedule
    {
        return $this->shiftScheduleService->updateSchedule($shiftSchedule, $request->validated());
    }

    public function destroy(ShiftSchedule $shiftSchedule)
    {
        $this->shiftScheduleService->deleteSchedule($shiftSchedule);

        return response()->json(null, 204);
    }
}
