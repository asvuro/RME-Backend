<?php

namespace Modules\BerkasKlaimClaimFile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\BerkasKlaimClaimFile\Models\ClaimFile;

class BerkasKlaimClaimFileController extends Controller
{
    public function index()
    {
        return ClaimFile::query()->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'visit_id' => ['required', 'exists:visits,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'status' => ['sometimes', Rule::in(ClaimFile::STATUSES)],
        ]);

        $data['claim_number'] = ClaimFile::generateClaimNumber();

        // Status awal klaim selalu draft -- status lanjutan hanya boleh
        // dicapai lewat transisi ALLOWED_TRANSITIONS di update(). Tanpa ini,
        // store() menerima status final apa pun (mis. paid) langsung saat
        // pembuatan, melewati mesin transisi yang baru saja dipasang.
        $data['status'] = ClaimFile::STATUS_DRAFT;

        return response()->json(ClaimFile::create($data)->refresh(), 201);
    }

    public function show(ClaimFile $claim_file)
    {
        return $claim_file;
    }

    public function update(Request $request, ClaimFile $claim_file)
    {
        $data = $request->validate([
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'submitted_at' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(ClaimFile::STATUSES)],
        ]);

        if (isset($data['status']) && $data['status'] !== $claim_file->status) {
            abort_if(
                ! in_array($data['status'], ClaimFile::ALLOWED_TRANSITIONS[$claim_file->status] ?? [], true),
                422,
                "Transisi status dari '{$claim_file->status}' ke '{$data['status']}' tidak diizinkan."
            );
        }

        $claim_file->update($data);

        return $claim_file;
    }

    public function destroy(ClaimFile $claim_file)
    {
        $claim_file->delete();

        return response()->json(null, 204);
    }
}
