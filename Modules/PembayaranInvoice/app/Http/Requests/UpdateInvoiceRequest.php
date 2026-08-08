<?php

namespace Modules\PembayaranInvoice\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('invoice')?->id;

        return [
            'invoice_number' => ['nullable', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($id)],
            // Dibatasi ke rentang pembulatan kas wajar -- bukan jalur untuk
            // menekan total_amount jadi negatif (lihat InvoiceService::updateInvoice).
            'rounding_adjustment' => ['sometimes', 'numeric', 'between:-999,999'],
            // 'status' SENGAJA tidak divalidasi di sini: status hanya boleh
            // berubah lewat InvoiceService::markPaid()/cancel() supaya lock
            // semantics & event InvoiceLocked selalu ikut, bukan status
            // dipalsukan lewat PUT biasa tanpa efek gerbang apa pun.
        ];
    }
}
