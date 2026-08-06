<?php

namespace Modules\LayananLabAnalyzerOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Payload endpoint POST lab-analyzer-orders/{order}/result - pengganti frame
 * HL7/ASTM sungguhan di versi tracking ini: teks hasil mentah apa adanya.
 */
class RecordLabAnalyzerOrderResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raw_result_text' => ['required', 'string'],
        ];
    }
}
