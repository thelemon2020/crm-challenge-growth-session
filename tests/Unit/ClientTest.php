<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Project;
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

    public function test_created_at_accessor_returns_formatted_date(): void
    {
        $client = Client::factory()->create(['created_at' => '1999-12-31']);

        $this->assertEquals("12/31/1999", $client->created_at);
    }

    public function test_client_has_many_projects(): void
    {
        $projectAmount = 2;
        $client = Client::factory()
            ->has(Project::factory()->count($projectAmount))
            ->create();

        $this->assertCount($projectAmount, $client->projects);
    }
}
