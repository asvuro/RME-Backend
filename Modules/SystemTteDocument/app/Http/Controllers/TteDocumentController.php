<?php

namespace Modules\SystemTteDocument\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SystemTteDocument\Http\Requests\SignTteDocumentRequest;
use Modules\SystemTteDocument\Http\Requests\StoreTteDocumentRequest;
use Modules\SystemTteDocument\Http\Resources\TteDocumentResource;
use Modules\SystemTteDocument\Models\TteDocument;
use Modules\SystemTteDocument\Services\TteDocumentService;

class TteDocumentController extends Controller
{
    public function __construct(private readonly TteDocumentService $documents)
    {
    }

    public function index(Request $request)
    {
        $query = TteDocument::query();

        if ($request->filled('ref_type')) {
            $query->where('ref_type', $request->string('ref_type'));
        }
        if ($request->filled('ref_id')) {
            $query->where('ref_id', $request->integer('ref_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return TteDocumentResource::collection(
            $query->latest('id')->paginate(min(100, (int) $request->input('per_page', 15))),
        );
    }

    public function show(TteDocument $tteDocument): TteDocumentResource
    {
        return new TteDocumentResource($tteDocument);
    }

    public function store(StoreTteDocumentRequest $request)
    {
        $document = $this->documents->create($request->validated());

        return (new TteDocumentResource($document))->response()->setStatusCode(201);
    }

    public function submitForSign(TteDocument $tteDocument): TteDocumentResource
    {
        $document = $this->documents->submitForSign($tteDocument->id);

        return new TteDocumentResource($document);
    }

    /**
     * Aksi inti state machine TTE internal: hitung document_hash + tandai SIGNED.
     * Tidak ada panggilan eksternal ke PSrE/BSrE -- itu future work.
     */
    public function sign(SignTteDocumentRequest $request, TteDocument $tteDocument): TteDocumentResource
    {
        $document = $this->documents->sign($tteDocument->id, (int) $request->validated('employee_id'));

        return new TteDocumentResource($document);
    }

    public function lock(TteDocument $tteDocument): TteDocumentResource
    {
        $document = $this->documents->lock($tteDocument->id);

        return new TteDocumentResource($document);
    }
}
