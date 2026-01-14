<?php

namespace Tests\Unit;

use App\Enums\StatusEnum;
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
            'status' => StatusEnum::Inactive
        ]);
        $activeClients = Client::factory()->count(2)->create([
            'status' => StatusEnum::Active
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

    public function test_soft_deleted_clients_are_not_in_default_queries(): void
    {
        // Arrange
        Client::factory()->count(4)->create();

        // Act
        Client::query()->delete();

        // Assert
        $this->assertCount(0, Client::all());
        $this->assertDatabaseCount('clients', 4);
        $this->assertCount(4, Client::withTrashed()->get());
    }
}
