<?php

namespace Tests\Unit\Support;

use App\Models\RsSetting;
use App\Support\RsSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RsSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RsSettingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(RsSettingService::class);
        Cache::forget(RsSettingService::CACHE_KEY);
    }

    public function test_get_mengembalikan_default_bila_kunci_belum_ada(): void
    {
        $this->assertNull($this->service->get('tidak.ada'));
        $this->assertTrue($this->service->get('tidak.ada', true));
        $this->assertSame('x', $this->service->get('tidak.ada', 'x'));
    }

    public function test_set_dan_get_tipe_bool_int_string_json(): void
    {
        $this->service->set('c.bool', true, 'bool');
        $this->service->set('c.bool_off', false, 'bool');
        $this->service->set('c.int', 42, 'int');
        $this->service->set('c.string', 'teks', 'string');
        $this->service->set('c.json', ['a' => 1, 'b' => ['c' => true]], 'json');

        $this->assertTrue($this->service->get('c.bool'));
        $this->assertFalse($this->service->get('c.bool_off'));
        $this->assertSame(42, $this->service->get('c.int'));
        $this->assertSame('teks', $this->service->get('c.string'));
        $this->assertSame(['a' => 1, 'b' => ['c' => true]], $this->service->get('c.json'));
    }

    public function test_get_membaca_baris_yang_dibuat_langsung_di_model(): void
    {
        RsSetting::create(['key' => 'manual.key', 'value' => 'TRUE', 'type' => 'bool']);

        Cache::forget(RsSettingService::CACHE_KEY);

        $this->assertTrue($this->service->get('manual.key'));
    }

    public function test_update_key_sama_menghasilkan_nilai_terbaru(): void
    {
        $this->service->set('kunci', 'lama', 'string');
        $this->service->set('kunci', 'baru', 'string');

        $this->assertSame('baru', $this->service->get('kunci'));
        $this->assertSame(1, RsSetting::query()->where('key', 'kunci')->count());
    }
}
