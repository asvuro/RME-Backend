<?php

namespace Modules\InventoryBloodBag\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\InventoryBloodBag\Models\BloodBag;
use Modules\InventoryBloodBag\Models\CrossmatchTest;
use Modules\InventoryBloodBag\Services\BloodBankService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class BloodBankServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BloodBankService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BloodBankService::class);
    }

    public function test_manual_release_allowed_before_reserved_until_expires(): void
    {
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_CROSSMATCH_RESERVED]);
        $test = CrossmatchTest::factory()->create([
            'blood_bag_id' => $bag->id,
            'reserved_until' => now()->addHours(10),
        ]);

        $this->service->release($test->id, false);

        $this->assertSame(BloodBag::STATUS_IN_STOCK, $bag->fresh()->status);
    }

    public function test_auto_release_rejected_when_reservation_not_yet_expired(): void
    {
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_CROSSMATCH_RESERVED]);
        $test = CrossmatchTest::factory()->create([
            'blood_bag_id' => $bag->id,
            'reserved_until' => now()->addHours(10),
        ]);

        try {
            $this->service->release($test->id, true);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        $this->assertSame(BloodBag::STATUS_CROSSMATCH_RESERVED, $bag->fresh()->status);
    }

    public function test_auto_release_allowed_when_reservation_expired(): void
    {
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_CROSSMATCH_RESERVED]);
        $test = CrossmatchTest::factory()->create([
            'blood_bag_id' => $bag->id,
            'reserved_until' => now()->subHour(),
        ]);

        $this->service->release($test->id, true);

        $this->assertSame(BloodBag::STATUS_IN_STOCK, $bag->fresh()->status);
    }

    public function test_mark_transfused_requires_crossmatch_reserved_status(): void
    {
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_IN_STOCK]);

        try {
            $this->service->markTransfused($bag->id);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }
    }

    public function test_mark_transfused_succeeds_from_crossmatch_reserved(): void
    {
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_CROSSMATCH_RESERVED]);

        $this->service->markTransfused($bag->id);

        $this->assertSame(BloodBag::STATUS_TRANSFUSED, $bag->fresh()->status);
    }

    public function test_compute_is_compatible_requires_all_three_results_negative(): void
    {
        $this->assertTrue(CrossmatchTest::computeIsCompatible('neg', 'neg', 'neg'));
        $this->assertFalse(CrossmatchTest::computeIsCompatible('pos', 'neg', 'neg'));
        $this->assertFalse(CrossmatchTest::computeIsCompatible('neg', 'pos', 'neg'));
        $this->assertFalse(CrossmatchTest::computeIsCompatible('neg', 'neg', 'pos'));
    }
}
