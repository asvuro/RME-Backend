<?php

namespace Modules\PembayaranDeposit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'refunded' sengaja tidak ada di sini: pengembalian dana harus
            // lewat DepositRefundController agar tercipta baris refund yang
            // tunduk pada batas kumulatif -- bukan flip status tanpa catatan.
            'status' => ['required', 'string', 'in:applied'],
        ];
    }
}
