<?php

namespace Tests\Unit;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_active_clients_using_active_scope(): void
    {
        Client::factory()->count(2)->create([
            'status' => 'inactive'
        ]);
        $activeClient = Client::factory()->create([
            'status' => 'active'
        ]);

        $activeClients = Client::active()->get();

        $this->assertEquals($activeClient->id, $activeClients->first()->id);
    }
}
