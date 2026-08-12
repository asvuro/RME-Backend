<?php

namespace Modules\PembayaranInvoiceItem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ];

        // Kebijakan yang sama dengan update(): harga satuan menentukan nilai
        // finansial tagihan, jadi hanya admin yang boleh menetapkannya bebas.
        // Petugas tidak mengirim unit_price -- harganya diambil dari tarif
        // katalog layanan (lihat InvoiceItemController::store). Gerbang
        // 403 identik tetap ada di controller sebagai lapis kedua.
        $rules['unit_price'] = [
            Rule::prohibitedIf(! $this->user()?->hasRole('admin')),
            'sometimes', 'numeric', 'min:0', 'max:999999999',
        ];

        return $rules;
    }
}
