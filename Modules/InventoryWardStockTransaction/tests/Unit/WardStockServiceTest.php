<?php

namespace Modules\InventoryWardStockTransaction\Tests\Unit;

use App\Modules\Contracts\HospitalConfig;
use App\Modules\Contracts\StockGate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralWard\Models\Ward;
use Modules\InventoryItem\Models\Item;
use Modules\InventoryWardItemStock\Models\WardItemStock;
use Modules\InventoryWardStockTransaction\Models\WardStockTransaction;
use Modules\InventoryWardStockTransaction\Services\WardStockService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Port inventory.transaksi_stok_ruangan + getStokAkhir simgos2:
 * ledger berjalan dengan saldo proyeksi yang dijaga konsisten.
 */
class WardStockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WardStockService $service;

    protected User $user;

    protected Ward $ward;

    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(WardStockService::class);
        $this->user = User::factory()->create();
        $this->ward = Ward::factory()->create();
        $this->item = Item::factory()->create();
    }

    public function test_service_terikat_sebagai_stock_gate(): void
    {
        $this->assertInstanceOf(WardStockService::class, app(StockGate::class));
    }

    public function test_adjust_in_menambah_saldo_dan_ledger(): void
    {
        $this->service->adjust($this->ward->id, $this->item->id, 'in', 100, $this->user);

        $this->assertSame(100, $this->service->currentStock($this->ward->id, $this->item->id));
        $this->assertSame(1, WardStockTransaction::query()->where('type', 'in')->count());
    }

    public function test_adjust_out_mengurangi_saldo_tanpa_negatif(): void
    {
        $this->service->adjust($this->ward->id, $this->item->id, 'in', 50, $this->user);
        $this->service->adjust($this->ward->id, $this->item->id, 'out', 30, $this->user);

        $this->assertSame(20, $this->service->currentStock($this->ward->id, $this->item->id));
    }

    public function test_adjust_out_melebihi_stok_ditolak_422(): void
    {
        $this->service->adjust($this->ward->id, $this->item->id, 'in', 10, $this->user);

        try {
            $this->service->adjust($this->ward->id, $this->item->id, 'out', 25, $this->user);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        // Saldo dan ledger tidak berubah oleh transaksi yang ditolak.
        $this->assertSame(10, $this->service->currentStock($this->ward->id, $this->item->id));
        $this->assertSame(1, WardStockTransaction::count());
    }

    public function test_allow_out_of_stock_true_meloloskan_negatif(): void
    {
        // Port PropertiConfig 48: ALLOW_ORDER_RESEP_STOK_KOSONG.
        app(HospitalConfig::class)->set('pharmacy.allow_order_out_of_stock', true, 'bool');

        $this->service->adjust($this->ward->id, $this->item->id, 'in', 10, $this->user);
        $this->service->adjust($this->ward->id, $this->item->id, 'out', 25, $this->user);

        // Saldo digenapkan nol (tidak pernah menyimpan negatif).
        $this->assertSame(0, $this->service->currentStock($this->ward->id, $this->item->id));
    }

    public function test_current_stock_nol_bila_belum_ada_mutasi(): void
    {
        $this->assertSame(0, $this->service->currentStock($this->ward->id, $this->item->id));
    }

    public function test_type_tak_dikenal_ditolak_422(): void
    {
        $this->assertThrows(
            fn () => $this->service->adjust($this->ward->id, $this->item->id, 'teleport', 5, $this->user),
            HttpException::class,
        );
    }

    public function test_saldo_proyeksi_selalu_cocok_rekap_ledger(): void
    {
        // Ala getStokAkhir: akumulasi ledger harus sama dengan saldo.
        $this->service->adjust($this->ward->id, $this->item->id, 'in', 100, $this->user);
        $this->service->adjust($this->ward->id, $this->item->id, 'out', 40, $this->user);
        $this->service->adjust($this->ward->id, $this->item->id, 'in', 15, $this->user);
        $this->service->adjust($this->ward->id, $this->item->id, 'out', 10, $this->user);

        // Ledger menyimpan jumlah positif; arah ada di type.
        $rekapLedger = (int) WardStockTransaction::query()
            ->where('ward_id', $this->ward->id)
            ->where('item_id', $this->item->id)
            ->get()
            ->sum(fn (WardStockTransaction $t) => $t->type === 'out' ? -$t->quantity : $t->quantity);
        $saldo = (int) WardItemStock::query()
            ->where('ward_id', $this->ward->id)
            ->where('item_id', $this->item->id)
            ->value('quantity');

        $this->assertSame(65, $rekapLedger);
        $this->assertSame($rekapLedger, $saldo);
    }

    public function test_ledger_menyimpan_jumlah_positif_dengan_arah_di_type(): void
    {
        // Skema ward_stock_transactions.quantity unsignedInteger - demo MariaDB
        // menangkap delta negatif ditolak DB meski lolos di sqlite test.
        $this->service->adjust($this->ward->id, $this->item->id, 'in', 30, $this->user);
        $this->service->adjust($this->ward->id, $this->item->id, 'out', 12, $this->user);

        $keluar = WardStockTransaction::query()->where('type', 'out')->first();
        $this->assertSame(12, (int) $keluar->quantity);
        $this->assertTrue($keluar->quantity >= 0);
    }
}
