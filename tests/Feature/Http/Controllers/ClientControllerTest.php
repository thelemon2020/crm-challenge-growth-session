<?php

namespace Tests\Feature\Http\Controllers;


use App\Enums\StatusEnum;
use App\Models\Client;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use LazilyRefreshDatabase;


    public function test_index_returns_all_clients()
    {
        $clients = Client::factory(5)->create();

        $response = $this->get(route('clients.index'));

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([
                ...$clients->pluck('name')->toArray()
            ]);
    }

    public function test_store_add_a_new_client()
    {
        $client = Client::factory()->raw(["status" => StatusEnum::Inactive->value]);

        $response = $this->post(route('clients.store'), $client);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', $client);
    }

    #[dataProvider('invalidClientData')]
    public function test_store_does_not_add_a_new_client_if_data_is_invalid(array $client)
    {
        $client = [...$client, 'status' => StatusEnum::Inactive->value];

        $response = $this->post(route('clients.store'), $client);

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

}
