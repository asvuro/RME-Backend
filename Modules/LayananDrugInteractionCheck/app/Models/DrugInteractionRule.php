<?php

namespace Modules\LayananDrugInteractionCheck\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\InventoryItem\Models\Item;
use Modules\LayananDrugInteractionCheck\Database\Factories\DrugInteractionRuleFactory;

/**
 * Aturan interaksi antar dua item obat master (pasangan unordered: A-B sama
 * artinya dengan B-A). Rule engine murni internal - tidak ada API farmasi
 * eksternal yang dikonsultasi.
 */
class DrugInteractionRule extends Model
{
    use HasFactory;

    public const SEVERITY_MINOR = 'minor';

    public const SEVERITY_MODERATE = 'moderate';

    public const SEVERITY_MAJOR_CONTRAINDICATED = 'major_contraindicated';

    /** Urutan ringan → berat, dipakai validasi + pengurutan hasil cek. */
    public const SEVERITIES = [
        self::SEVERITY_MINOR,
        self::SEVERITY_MODERATE,
        self::SEVERITY_MAJOR_CONTRAINDICATED,
    ];

    protected $fillable = [
        'item_id_a',
        'item_id_b',
        'severity',
        'clinical_note',
    ];

    public function itemA(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id_a');
    }

    public function itemB(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id_b');
    }

    protected static function newFactory(): DrugInteractionRuleFactory
    {
        return DrugInteractionRuleFactory::new();
    }
}
