<?php

namespace Tests\Feature;

use App\Models\StoreType;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorRateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $storeType = StoreType::create([
            'name' => 'Test Type',
        ]);

        $this->vendor = Vendor::create([
            'owner_name' => 'Test Owner',
            'phone' => '01000000123',
            'password' => bcrypt('password'),
            'store_name' => 'Test Store',
            'store_type_id' => $storeType->id,
            'is_active' => true,
            'is_verified' => true,
            'rate' => 0.00,
        ]);
    }

    public function test_user_can_rate_vendor_successfully(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 4.5,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('code', 200);
        $response->assertJsonPath('data.rate', 4.5);

        $this->assertDatabaseHas('vendor_rates', [
            'user_id' => $this->user->id,
            'vendor_id' => $this->vendor->id,
            'rate' => 4.50,
        ]);

        $this->assertEquals(4.50, $this->vendor->fresh()->rate);
    }

    public function test_user_cannot_rate_vendor_multiple_times(): void
    {
        // First rate
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 4.5,
            ])->assertStatus(200);

        // Second rate should fail
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 5,
            ]);

        $response->assertStatus(422);
        $response->assertJsonPath('code', 422);
    }

    public function test_user_can_update_rating(): void
    {
        // Rate first
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 3.5,
            ])->assertStatus(200);

        // Update rating
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 4.8,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.rate', 4.8);

        $this->assertDatabaseHas('vendor_rates', [
            'user_id' => $this->user->id,
            'vendor_id' => $this->vendor->id,
            'rate' => 4.80,
        ]);

        $this->assertEquals(4.80, $this->vendor->fresh()->rate);
    }

    public function test_vendor_average_rate_recalculated_correctly_multiple_users(): void
    {
        $user2 = User::factory()->create();

        // User 1 rates 3.5
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 3.5,
            ])->assertStatus(200);

        $this->assertEquals(3.50, $this->vendor->fresh()->rate);

        // User 2 rates 4.2
        $this->actingAs($user2, 'sanctum')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 4.2,
            ])->assertStatus(200);

        // Average should be (3.5 + 4.2) / 2 = 3.85
        $this->assertEquals(3.85, $this->vendor->fresh()->rate);

        // User 1 updates to 4.9
        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 4.9,
            ])->assertStatus(200);

        // Average should be (4.9 + 4.2) / 2 = 4.55
        $this->assertEquals(4.55, $this->vendor->fresh()->rate);
    }

    public function test_rating_messages_are_localized(): void
    {
        // Test English response
        $responseEn = $this->actingAs($this->user, 'sanctum')
            ->withHeader('Accept-Language', 'en')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 4.5,
            ]);

        $responseEn->assertStatus(200);
        $responseEn->assertJsonPath('message', 'Rating submitted successfully');

        // Test Arabic response for duplicate rating
        $responseAr = $this->actingAs($this->user, 'sanctum')
            ->withHeader('Accept-Language', 'ar')
            ->postJson("/api/vendors/{$this->vendor->id}/rate", [
                'rate' => 5,
            ]);

        $responseAr->assertStatus(422);
        $responseAr->assertJsonPath('message', 'لقد قمت بتقييم هذا المتجر بالفعل. يمكنك تحديث تقييمك بدلاً من ذلك.');
    }
}
