<?php

namespace Tests\Feature\Http\Controllers;


use App\Enums\StatusEnum;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setting up roles and permissions
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        $manageUsersPermission = Permission::create(['name' => 'manage users']);
        $manageClientsPermission = Permission::create(['name' => 'manage clients']);
        $manageProjectsPermission = Permission::create(['name' => 'manage projects']);
        $manageTasksPermission = Permission::create(['name' => 'manage tasks']);
        $viewOwnProjectsPermission = Permission::create(['name' => 'view own projects']);
        $viewOwnTasksPermission = Permission::create(['name' => 'view own tasks']);

        $adminRole->syncPermissions([
            $manageUsersPermission,
            $manageClientsPermission,
            $manageProjectsPermission,
            $manageTasksPermission,
            $viewOwnProjectsPermission,
            $viewOwnTasksPermission,
        ]);

        $userRole->syncPermissions([
            $viewOwnProjectsPermission,
            $viewOwnTasksPermission,
        ]);

        // Setting up users
        $this->user = User::factory()->user()->create();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_list_clients_requires_authentication()
    {
        $this->get(route('clients.index'))
            ->assertRedirect('/login');
    }

    public function test_admin_can_list_all_clients()
    {
        // Arrange
        $clients = Client::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($this->admin)->get(route('clients.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSeeInOrder($clients->pluck('name')->toArray());
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Dashboard')
            ->has('clients', $clients->count()));
    }

    public function test_user_cannot_list_clients_without_permission()
    {
        // Arrange
        Client::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($this->user)->get(route('clients.index'));

        // Assert
        $response->assertForbidden();
    }

    public function test_create_client_requires_authentication()
    {
        // Arrange
        $client = Client::factory()->raw(["status" => StatusEnum::Inactive->value]);

        // Act
        $response = $this->post(route('clients.store'), $client);

        // Assert
        $response->assertRedirect('/login');
    }

    public function test_can_create_client_with_valid_data()
    {
        // Arrange
        $client = Client::factory()->raw(["status" => StatusEnum::Inactive->value]);

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('clients.store'), $client);

        // Assert
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success', 'Client created!');
        $this->assertDatabaseHas('clients', $client);
    }

    #[dataProvider('invalidClientData')]
    public function test_cannot_create_client_with_invalid_data(array $client)
    {
        // Arrange
        $client['status'] = StatusEnum::Inactive->value;

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('clients.store'), $client);

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

    public function test_show_single_client_with_projects_requires_authentication()
    {
        // Arrange
        $client = Client::factory()->has(Project::factory())->create();

        // Act
        $response = $this->get(route('clients.show', $client));

        // Assert
        $response->assertRedirect('/login');
    }

    public function test_can_show_single_client_with_projects()
    {
        // Arrange
        $client = Client::factory()->has(Project::factory())->create();
        $project = $client->projects->first();

        // Act
        $response = $this->actingAs($this->admin)->get(route('clients.show', $client));

        // Assert
        $response
            ->assertStatus(200)
            ->assertSeeInOrder([
                $client->name,
                $project->name,
                $project->description,
                $project->status,
            ]);
    }

    public function test_update_client_requires_authentication()
    {
        // Arrange
        $client = client::factory()->create();
        $clientWithAura = client::factory()->raw(["name" => "Aura"]);

        // Act
        $clientWithAura['status'] = StatusEnum::Active->value;

        // Assert
        $response = $this->put(route('clients.update', $client), $clientWithAura);
        $response->assertRedirect('/login');
    }

    public function test_can_update_client_with_valid_data()
    {
        // Arrange
        $client = client::factory()->create();
        $clientWithAura = client::factory()->raw(["name" => "Aura"]);

        // Act
        $clientWithAura['status'] = StatusEnum::Active->value;

        // Assert
        $response = $this->actingAs($this->admin)->put(route('clients.update', $client), $clientWithAura);
        $response->assertRedirect(route('clients.show', $client));
        $this->assertDatabaseHas('clients', [...$clientWithAura, 'id' => $client->id]);
    }

    #[dataProvider('invalidClientUpdateData')]
    public function test_cannot_update_client_with_invalid_data(Client $clientUpdateData)
    {
        // Arrange
        $currentClient = Client::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->patch(route('clients.update', $currentClient), $clientUpdateData->toArray());

        // Assert
        $response
            ->assertSessionHasErrors()
            ->assertRedirect();

        $this->assertDatabaseMissing('clients', ['id' => $currentClient->id, ...$clientUpdateData->toArray()]);
    }

    public static function invalidClientUpdateData(): array
    {
        return [
            'missing name' => ['clientUpdateData' => Client::factory()->make(["name" => ''])],
            'missing email' => ['clientUpdateData' => Client::factory()->make(["email" => ''])],
            'missing company' => ['clientUpdateData' => Client::factory()->make(["company" => ''])],
            'missing address' => ['clientUpdateData' => Client::factory()->make(["address" => ''])],
        ];
    }

    public function test_cannot_update_client_with_invalid_status()
    {
        // Arrange
        $currentClient = Client::factory()->create();
        $clientUpdateData = Client::factory()->raw();
        $clientUpdateData['status'] = 'invalid-status';

        // Act
        $response = $this->actingAs($this->admin)
            ->patch(route('clients.update', $currentClient), $clientUpdateData);

        // Assert
        $response
            ->assertSessionHasErrors()
            ->assertRedirect();

        $this->assertDatabaseMissing('clients', ['id' => $currentClient->id, ...$clientUpdateData]);
    }

    public function test_delete_client_requires_authentication()
    {
        // Arrange
        $clients = Client::factory(3)->create();

        // Act
        $response = $this->delete(route('clients.destroy', $clients->first()));

        // Assert
        $response->assertRedirect('/login');
    }

    public function test_can_soft_delete_client()
    {
        // Arrange
        $clients = Client::factory(3)->create();

        // Act
        $response = $this->actingAs($this->admin)->delete(route('clients.destroy', $clients->first()));

        // Assert
        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseCount('clients', 3);
        $this->assertCount(2, Client::all());
    }

    public function test_can_show_create_client_page()
    {
        // Act
        $response = $this->actingAs($this->admin)->get(route('clients.create'));

        // Assert
        $response->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Create'));
    }
}
