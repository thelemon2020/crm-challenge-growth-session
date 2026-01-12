<?php

namespace Tests\Unit;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope_only_returns_active_clients(): void
    {
        $inactiveClients = Client::factory()->count(2)->create([
            'status' => 'inactive'
        ]);
        $activeClients = Client::factory()->count(2)->create([
            'status' => 'active'
        ]);

        $this->assertEquals($activeClients->pluck('id')->toArray(), Client::active()->pluck('id')->toArray());
    }
}
