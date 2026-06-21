<?php

namespace Tests\Feature;

use App\Models\DeliveryUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeliveryProfileTest extends TestCase
{
    use RefreshDatabase;

    protected DeliveryUser $deliveryUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->deliveryUser = DeliveryUser::create([
            'name' => 'John Doe',
            'phone' => '01012345678',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'img' => 'delivaries/avatar.png',
            'front_ident' => 'delivaries/front.png',
            'back_ident' => 'delivaries/back.png',
            'personal_deriving_license' => 'delivaries/personal_license.png',
            'machine_license' => 'delivaries/machine_license.png',
            'active_status' => true,
            'ban' => false,
            'approval_status' => 'approved',
            'lat' => 30.0444,
            'lng' => 31.2357,
            'balance' => 100.50,
            'max_active_orders' => 3,
        ]);
    }

    public function test_guest_cannot_access_profile_endpoints(): void
    {
        $this->getJson('/delivery/profile')->assertStatus(401);
        $this->postJson('/delivery/profile', [])->assertStatus(401);
    }

    public function test_authenticated_delivery_driver_can_retrieve_profile(): void
    {
        $response = $this->actingAs($this->deliveryUser, 'sanctum')
            ->getJson('/delivery/profile');

        $response->assertStatus(200)
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.name', 'John Doe')
            ->assertJsonPath('data.phone', '01012345678')
            ->assertJsonPath('data.email', 'john@example.com')
            ->assertJsonPath('data.approval_status', 'approved');
    }

    public function test_delivery_driver_can_update_profile_details(): void
    {
        $response = $this->actingAs($this->deliveryUser, 'sanctum')
            ->postJson('/delivery/profile', [
                'name' => 'John Updated',
                'phone' => '01087654321',
                'email' => 'updated@example.com',
                'lat' => 31.1234,
                'lng' => 32.5678,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.name', 'John Updated')
            ->assertJsonPath('data.phone', '01087654321')
            ->assertJsonPath('data.email', 'updated@example.com')
            ->assertJsonPath('data.lat', 31.1234)
            ->assertJsonPath('data.lng', 32.5678);

        $this->assertDatabaseHas('delivery_users', [
            'id' => $this->deliveryUser->id,
            'name' => 'John Updated',
            'phone' => '01087654321',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_delivery_driver_can_update_password(): void
    {
        $response = $this->actingAs($this->deliveryUser, 'sanctum')
            ->postJson('/delivery/profile', [
                'password' => 'newpassword123',
            ]);

        $response->assertStatus(200);

        $this->deliveryUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->deliveryUser->password));
    }

    public function test_profile_update_validation_uniqueness(): void
    {
        $otherDriver = DeliveryUser::create([
            'name' => 'Other Driver',
            'phone' => '01099999999',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front2.png',
            'back_ident' => 'delivaries/back2.png',
            'personal_deriving_license' => 'delivaries/personal_license2.png',
            'machine_license' => 'delivaries/machine_license2.png',
        ]);

        // Attempting to update to other's phone should fail
        $response = $this->actingAs($this->deliveryUser, 'sanctum')
            ->postJson('/delivery/profile', [
                'phone' => '01099999999',
            ]);

        $response->assertStatus(422);

        // Attempting to update to other's email should fail
        $response2 = $this->actingAs($this->deliveryUser, 'sanctum')
            ->postJson('/delivery/profile', [
                'email' => 'other@example.com',
            ]);

        $response2->assertStatus(422);
    }

    public function test_profile_update_keeps_existing_files_if_not_provided(): void
    {
        $response = $this->actingAs($this->deliveryUser, 'sanctum')
            ->postJson('/delivery/profile', [
                'name' => 'John Just Name',
            ]);

        $response->assertStatus(200);

        $this->deliveryUser->refresh();
        $this->assertEquals('delivaries/avatar.png', $this->deliveryUser->img);
        $this->assertEquals('delivaries/front.png', $this->deliveryUser->front_ident);
    }

    public function test_profile_update_with_images_replaces_old_files(): void
    {
        // Create a temporary directory under public/delivaries if not exist to fake storage paths for local disk testing
        $oldImgPath = public_path($this->deliveryUser->img);
        $oldFrontPath = public_path($this->deliveryUser->front_ident);

        @mkdir(dirname($oldImgPath), 0755, true);
        file_put_contents($oldImgPath, 'dummy image content');
        file_put_contents($oldFrontPath, 'dummy front identity content');

        $this->assertTrue(file_exists($oldImgPath));
        $this->assertTrue(file_exists($oldFrontPath));

        $newImg = UploadedFile::fake()->image('new_avatar.jpg');
        $newFront = UploadedFile::fake()->image('new_front.jpg');

        $response = $this->actingAs($this->deliveryUser, 'sanctum')
            ->postJson('/delivery/profile', [
                'img' => $newImg,
                'front_ident' => $newFront,
            ]);

        $response->assertStatus(200);

        // Assert that the old files were deleted from public disk (unlinked)
        $this->assertFalse(file_exists($oldImgPath));
        $this->assertFalse(file_exists($oldFrontPath));

        // Get the new paths
        $this->deliveryUser->refresh();
        $newImgStoredPath = public_path($this->deliveryUser->img);
        $newFrontStoredPath = public_path($this->deliveryUser->front_ident);

        // Clean up newly created fake files
        @unlink($newImgStoredPath);
        @unlink($newFrontStoredPath);
    }
}
