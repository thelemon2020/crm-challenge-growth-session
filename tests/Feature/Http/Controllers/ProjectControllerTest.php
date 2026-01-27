<?php

namespace Feature\Http\Controllers;


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

class ProjectControllerTest extends TestCase
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

    // INDEX
    // authentication, authorization (permission), success, fail
    public function test_list_projects_requires_authentication()
    {
        // Act
        $response = $this->get(route('projects.index'));

        // Assert
        $response->assertRedirect('/login');
    }

    public function test_admin_view_all_projects()
    {
        // Arrange
        $projects = Project::factory()->count(10)->create();

        // Act
        $response = $this->actingAs($this->admin)->get(route('projects.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSeeInOrder($projects->pluck('id')->toArray());
        $response->assertInertia(fn ($page) => $page
            ->component('Projects/Index')
            ->has('projects.data', $projects->count())
        );
    }

    public function test_user_only_view_own_projects()
    {
        // Arrange
        $userProjects = Project::factory()->count(10)->create([
            'user_id' => $this->user->id
        ]);
        $otherProjects = Project::factory()->count(10)->create();

        // Act
        $response = $this->actingAs($this->user)->get(route('projects.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSeeInOrder($userProjects->pluck('id')->toArray());
        $response->assertInertia(fn ($page) => $page
            ->component('Projects/Index')
            ->has('projects.data', $userProjects->count())
        );
    }


    // CREATE
    public function test_show_create_project_page_requires_authentication()
    {
        // Act
        $response = $this->get(route('projects.create'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_show_create_project_page()
    {
        $this->markTestSkipped();
    }

    public function test_admin_can_show_create_project_page()
    {
        $this->markTestSkipped();
    }

    // STORE
    public function test_create_project_page_requires_authentication()
    {
        $this->markTestSkipped();
    }

    public function test_admin_can_create_project_with_valid_data()
    {
        $this->markTestSkipped();
    }

    public function test_user_can_create_project_with_valid_data()
    {
        $this->markTestSkipped();
    }

    public function test_cannot_create_project_with_invalid_data()
    {
        $this->markTestSkipped();
    }



    // EDIT


    // UPDATE


    // DESTROY

}
