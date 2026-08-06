<?php

namespace Modules\LayananLabAnalyzerOrder\Services;

use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerVendor;

class LabAnalyzerVendorService
{
    public function create(array $data): LabAnalyzerVendor
    {
        return LabAnalyzerVendor::create($data);
    }

    public function update(LabAnalyzerVendor $vendor, array $data): LabAnalyzerVendor
    {
        $vendor->update($data);

        return $vendor;
    }

    /**
     * Vendor masih dirujuk order analyzer tidak boleh terhapus - FK-nya
     * nullOnDelete, jadi tanpa gerbang ini riwayat order akan kehilangan
     * identitas analyzer secara diam-diam.
     */
    public function destroy(LabAnalyzerVendor $vendor): void
    {
        abort_if(
            $vendor->orders()->exists(),
            422,
            "Vendor \"{$vendor->vendor_name}\" masih dipakai order analyzer.",
        );

        $vendor->delete();
    }
}
