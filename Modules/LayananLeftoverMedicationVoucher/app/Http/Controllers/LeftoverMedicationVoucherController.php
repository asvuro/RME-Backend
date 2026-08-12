<?php

namespace Modules\LayananLeftoverMedicationVoucher\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LayananLeftoverMedicationVoucher\Http\Requests\StoreLeftoverMedicationVoucherRequest;
use Modules\LayananLeftoverMedicationVoucher\Http\Requests\UpdateLeftoverMedicationVoucherRequest;
use Modules\LayananLeftoverMedicationVoucher\Http\Resources\LeftoverMedicationVoucherResource;
use Modules\LayananLeftoverMedicationVoucher\Models\LeftoverMedicationVoucher;

class LeftoverMedicationVoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = LeftoverMedicationVoucher::query();

        return LeftoverMedicationVoucherResource::collection($query->orderBy('id', 'desc')->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreLeftoverMedicationVoucherRequest $request)
    {
        $data = $request->validated();

        // Voucher selalu lahir pending dan redeemed_at distempel server saat
        // transisi redeem di update() -- kebijakan yang sama dengan gerbang
        // forward-only di sana. Tanpa ini voucher bisa lahir dalam status
        // redeemed dengan timestamp kiriman klien (audit trail palsu).
        $data['status'] = 'pending';
        unset($data['redeemed_at']);

        $voucher = LeftoverMedicationVoucher::create($data);

        return (new LeftoverMedicationVoucherResource($voucher))->response()->setStatusCode(201);
    }

    public function show(LeftoverMedicationVoucher $voucher): LeftoverMedicationVoucherResource
    {
        return new LeftoverMedicationVoucherResource($voucher);
    }

    /**
     * Gerbang forward-only: pending->redeemed dan pending->expired saja.
     * Sekali redeemed/expired, status tidak bisa diubah lagi lewat endpoint
     * ini (mencegah reset ke pending lalu redeem ulang). redeemed_at
     * distempel server saat transisi ke redeemed, bukan dari input klien.
     */
    public function update(UpdateLeftoverMedicationVoucherRequest $request, LeftoverMedicationVoucher $voucher): LeftoverMedicationVoucherResource
    {
        $data = $request->validated();

        $voucher = DB::transaction(function () use ($data, $voucher) {
            $voucher = LeftoverMedicationVoucher::query()->whereKey($voucher->id)->lockForUpdate()->firstOrFail();

            if (isset($data['status'])) {
                abort_if(
                    $voucher->status !== 'pending',
                    422,
                    "Voucher berstatus '{$voucher->status}' tidak dapat diubah statusnya lagi."
                );
                abort_if(
                    ! in_array($data['status'], ['redeemed', 'expired'], true),
                    422,
                    'Transisi status voucher hanya boleh ke redeemed atau expired.'
                );

                if ($data['status'] === 'redeemed') {
                    $data['redeemed_at'] = now();
                }
            }

            $voucher->update($data);

            return $voucher;
        });

        return new LeftoverMedicationVoucherResource($voucher);
    }
}
