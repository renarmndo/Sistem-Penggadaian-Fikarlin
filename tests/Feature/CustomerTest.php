<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_customer_with_identity_number(): void
    {
        $customer = Customer::create([
            'identity_number' => '1234567890123456',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10',
            'notes' => 'Pelanggan VIP',
        ]);

        $this->assertDatabaseHas('customers', [
            'identity_number' => '1234567890123456',
            'name' => 'Budi Santoso',
        ]);

        $this->assertEquals('1234567890123456', $customer->identity_number);
    }

    public function test_identity_number_must_have_exactly_16_digits(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('petugas.customers.store'), [
            'identity_number' => '12345',
            'name' => 'Budi Santoso',
        ]);

        $response->assertSessionHasErrors('identity_number');
        $this->assertDatabaseMissing('customers', ['identity_number' => '12345']);
    }

    public function test_customer_can_be_soft_deleted(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);
        $customer = Customer::create([
            'identity_number' => '1234567890123456',
            'name' => 'Budi Santoso',
        ]);

        $response = $this->actingAs($user)->delete(route('petugas.customers.destroy', $customer));

        $response->assertRedirect(route('petugas.customers.index'));
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_identity_number_must_be_unique(): void
    {
        Customer::create([
            'identity_number' => '1234567890123456',
            'name' => 'Budi Santoso',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Customer::create([
            'identity_number' => '1234567890123456',
            'name' => 'Santoso Budi',
        ]);
    }

    public function test_customer_scope_search(): void
    {
        Customer::create([
            'identity_number' => '3201999988880001',
            'name' => 'Ahmad Dahlan',
            'phone' => '081987654321',
        ]);

        Customer::create([
            'identity_number' => '3201999988880002',
            'name' => 'Siti Rahma',
            'phone' => '081555444333',
        ]);

        $resultsByKtp = Customer::search('3201999988880001')->get();
        $this->assertCount(1, $resultsByKtp);
        $this->assertEquals('Ahmad Dahlan', $resultsByKtp->first()->name);

        $resultsByName = Customer::search('Siti')->get();
        $this->assertCount(1, $resultsByName);
        $this->assertEquals('Siti Rahma', $resultsByName->first()->name);
    }
}
