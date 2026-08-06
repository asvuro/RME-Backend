<?php

namespace Modules\InventoryBloodBag\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\InventoryBloodBag\Http\Resources\CrossmatchTestResource;
use Modules\InventoryBloodBag\Models\CrossmatchTest;
use Modules\InventoryBloodBag\Services\BloodBankService;

/**
 * Pembuatan CrossmatchTest sengaja TIDAK punya endpoint store generik —
 * hanya lewat POST blood-bags/{bag}/crossmatch (lihat BloodBagController)
 * karena pembuatan tes selalu terikat gerbang reservasi kantong terkait.
 */
class CrossmatchTestController extends Controller
{
    public function __construct(protected BloodBankService $bloodBankService)
    {
    }

    public function index(Request $request)
    {
        $query = CrossmatchTest::query();

        if ($request->filled('blood_bag_id')) {
            $query->where('blood_bag_id', $request->integer('blood_bag_id'));
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->integer('patient_id'));
        }

        return CrossmatchTestResource::collection($query->latest()->paginate($request->integer('per_page', 15)));
    }

    public function show(CrossmatchTest $crossmatch_test): CrossmatchTestResource
    {
        return new CrossmatchTestResource($crossmatch_test);
    }

    public function release(CrossmatchTest $crossmatch_test)
    {
        $test = $this->bloodBankService->release($crossmatch_test->id);

        return new CrossmatchTestResource($test);
    }
}
