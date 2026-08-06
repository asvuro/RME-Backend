<?php

namespace Modules\InventoryBloodBag\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\InventoryBloodBag\Http\Requests\StoreBloodBagRequest;
use Modules\InventoryBloodBag\Http\Requests\StoreCrossmatchTestRequest;
use Modules\InventoryBloodBag\Http\Requests\UpdateBloodBagRequest;
use Modules\InventoryBloodBag\Http\Resources\BloodBagResource;
use Modules\InventoryBloodBag\Http\Resources\CrossmatchTestResource;
use Modules\InventoryBloodBag\Models\BloodBag;
use Modules\InventoryBloodBag\Services\BloodBankService;

class BloodBagController extends Controller
{
    public function __construct(protected BloodBankService $bloodBankService)
    {
    }

    public function index(Request $request)
    {
        $query = BloodBag::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('blood_type_id')) {
            $query->where('blood_type_id', $request->integer('blood_type_id'));
        }

        return BloodBagResource::collection($query->latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreBloodBagRequest $request)
    {
        // Boleh create Model langsung: pembuatan kantong baru bukan
        // transisi state machine (belum ada status lama untuk digerbangi),
        // beda dengan crossmatch/release/markTransfused yang lewat service.
        // refresh(): 'status' bisa lahir dari default DB (bukan payload),
        // atribut di memori harus disinkron sebelum diserialisasi Resource.
        $bag = BloodBag::create($request->validated())->refresh();

        return (new BloodBagResource($bag))->response()->setStatusCode(201);
    }

    public function show(BloodBag $blood_bag): BloodBagResource
    {
        return new BloodBagResource($blood_bag);
    }

    public function update(UpdateBloodBagRequest $request, BloodBag $blood_bag): BloodBagResource
    {
        $blood_bag->update($request->validated());

        return new BloodBagResource($blood_bag->fresh());
    }

    public function destroy(BloodBag $blood_bag)
    {
        abort_if(
            $blood_bag->status !== BloodBag::STATUS_IN_STOCK,
            422,
            "Kantong darah #{$blood_bag->id} berstatus {$blood_bag->status}, hanya kantong in_stock yang bisa dihapus.",
        );

        $blood_bag->delete();

        return response()->json(null, 204);
    }

    public function crossmatch(StoreCrossmatchTestRequest $request, BloodBag $blood_bag)
    {
        $test = $this->bloodBankService->performCrossmatch(
            $blood_bag->id,
            $request->validated(),
            $request->user(),
        );

        return (new CrossmatchTestResource($test))->response()->setStatusCode(201);
    }

    /**
     * Bukan bagian eksplisit spesifikasi endpoint, tapi BloodBankService::
     * markTransfused() perlu jalur HTTP supaya gerbangnya benar-benar
     * terpakai (bukan dead code) — melengkapi crossmatch/release.
     */
    public function transfuse(BloodBag $blood_bag)
    {
        $bag = $this->bloodBankService->markTransfused($blood_bag->id);

        return new BloodBagResource($bag);
    }
}
