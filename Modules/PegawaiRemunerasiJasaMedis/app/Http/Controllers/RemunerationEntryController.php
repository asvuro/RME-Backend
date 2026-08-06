<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PegawaiRemunerasiJasaMedis\Http\Requests\StoreRemunerationEntryRequest;
use Modules\PegawaiRemunerasiJasaMedis\Http\Requests\UpdateRemunerationEntryRequest;
use Modules\PegawaiRemunerasiJasaMedis\Models\RemunerationEntry;
use Modules\PegawaiRemunerasiJasaMedis\Services\RemunerationService;

class RemunerationEntryController extends Controller
{
    public function __construct(protected RemunerationService $remunerationService)
    {
    }

    public function index(Request $request)
    {
        $query = RemunerationEntry::query();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->string('source_type'));
        }

        return $query->orderByDesc('service_date')->paginate(15);
    }

    /**
     * GET remuneration-entries/summary?employee_id=&month=&year=
     * Didaftarkan sebelum apiResource show di routes/api.php supaya "summary"
     * tidak tertangkap sebagai {remuneration_entry}.
     */
    public function summary(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        return response()->json(
            $this->remunerationService->summary($data['employee_id'], $data['month'], $data['year']),
        );
    }

    public function store(StoreRemunerationEntryRequest $request)
    {
        $entry = $this->remunerationService->create($request->validated());

        return response()->json($entry->refresh(), 201);
    }

    public function show(RemunerationEntry $remunerationEntry): RemunerationEntry
    {
        return $remunerationEntry;
    }

    public function update(UpdateRemunerationEntryRequest $request, RemunerationEntry $remunerationEntry)
    {
        return $this->remunerationService->update($remunerationEntry, $request->validated());
    }

    public function destroy(RemunerationEntry $remunerationEntry)
    {
        $this->remunerationService->delete($remunerationEntry);

        return response()->json(null, 204);
    }
}
