<?php

namespace Tests\Feature\Http\Controllers;


use App\Enums\StatusEnum;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_list_all_clients()
    {
        // Arrange
        $clients = Client::factory(5)->create();

        // Act
        $response = $this->get(route('clients.index'));

        // Assert
        $response
            ->assertStatus(200)
            ->assertSeeInOrder([
                ...$clients->pluck('name')->toArray()
            ]);
    }

    public function test_can_create_client_with_valid_data()
    {
        // Arrange
        $client = Client::factory()->raw(["status" => StatusEnum::Inactive->value]);

        // Act
        $response = $this->post(route('clients.store'), $client);

        // Assert
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success', 'Client created!');
        $this->assertDatabaseHas('clients', $client);
    }

    #[dataProvider('invalidClientData')]
    public function test_cannot_create_client_with_invalid_data(array $client)
    {
        // Arrange
        $client = [...$client, 'status' => StatusEnum::Inactive->value];

        // Act
        $response = $this->post(route('clients.store'), $client);

        // Assert
        $response
            ->assertSessionHasErrors()
            ->assertRedirect();

        $this->assertDatabaseMissing('clients', $client);
    }

    public static function invalidClientData(): array
    {
        return [
            'missing name' => ['client' => Client::factory()->raw(["name" => ''])],
            'missing email' => ['client' => Client::factory()->raw(["email" => ''])],
            'missing company' => ['client' => Client::factory()->raw(["company" => ''])],
            'missing address' => ['client' => Client::factory()->raw(["address" => ''])],
        ];
    }

    public function test_can_show_single_client_with_projects()
    {
        $client = Client::factory()->has(Project::factory())->create();
        $project = $client->projects->first();

        $response = $this->get(route('clients.show', $client));

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([
                $client->name,
                $project->name,
                $project->description,
                $project->status,
            ]);
    }

    public function test_can_update_client_with_valid_data()
    {
        $client = client::factory()->create();
        $clientWithAura = client::factory()->raw(["name" => "Aura"]);

        $clientWithAura = [...$clientWithAura, 'status' => StatusEnum::Active->value];

        $response = $this->put(route('clients.update', $client), $clientWithAura);
        $response->assertRedirect(route('clients.show', $client));
        $this->assertDatabaseHas('clients', [...$clientWithAura, 'id' => $client->id]);
    }

    public function test_cannot_update_client_with_invalid_data()
    {
        $this->markTestSkipped();
    }


    // Authorisation/Permission test
    public function test_admin_can_list_all_clients()
    {
        $this->markTestSkipped();
    }

    public function test_user_cannot_list_clients_without_permission()
    {
        $this->markTestSkipped();
    }
}
