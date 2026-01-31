<?php

namespace Feature\Http\Controllers;


use App\Enums\ClientStatusEnum;
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
        $response = $this->get(route('projects.index'));

        $response->assertRedirect('/login');
    }

    public function test_admin_view_all_projects()
    {
        $projects = Project::factory()->count(10)->create();

        $response = $this->actingAs($this->admin)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSeeInOrder($projects->pluck('id')->toArray());
        $response->assertInertia(fn($page) => $page
            ->component('Project/Index')
            ->has('projects.data', $projects->count())
        );
    }

    public function test_user_only_view_own_projects()
    {
        $userProjects = Project::factory()->count(10)->create([
            'user_id' => $this->user->id
        ]);
        $otherProjects = Project::factory()->count(10)->create();

        $response = $this->actingAs($this->user)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSeeInOrder($userProjects->pluck('id')->toArray());
        $response->assertInertia(fn($page) => $page
            ->component('Project/Index')
            ->has('projects.data', $userProjects->count())
        );
    }


    // CREATE
    public function test_show_create_project_page_requires_authentication()
    {
        $response = $this->get(route('projects.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_show_create_project_page()
    {
        $response = $this->actingAs($this->admin)->get(route('projects.create'));

        $response->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Project/Create'));
    }

    public function test_user_can_show_create_project_page()
    {
        $response = $this->actingAs($this->user)->get(route('projects.create'));

        $response->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Project/Create'));
    }

    // STORE
    public function test_create_project_requires_authentication()
    {
        $response = $this->post(route('projects.store'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_create_project_with_valid_data()
    {
        $project = Project::factory()->raw();
        $project['deadline'] = '2026-02-24 03:45:20';

        $response = $this->actingAs($this->admin)->post(route('projects.store'), $project);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseCount('projects', 1);
        $this->assertDatabaseHas('projects', $project);
    }

    public function test_user_can_create_project_with_valid_data()
    {
        $project = Project::factory()->raw();
        $project['deadline'] = '2026-02-24 03:45:20';

        $response = $this->actingAs($this->user)->post(route('projects.store'), $project);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseCount('projects', 1);
        $this->assertDatabaseHas('projects', $project);
    }

    #[dataProvider('invalidProjectData')]
    public function test_cannot_create_project_with_invalid_data(array $project)
    {
        $project['deadline'] = '2026-02-24 03:45:20';

        $response = $this->actingAs($this->user)->post(route('projects.store'), $project);

        $response->assertSessionHasErrors()->assertRedirect();
        $this->assertDatabaseMissing('projects', $project);
    }

    public static function invalidProjectData(): array
    {
        return [
            'missing client' => ['project' => Project::factory()->raw(['client' => null])],
            'missing user' => ['project' => Project::factory()->raw(['user' => null])],
            'missing title' => ['project' => Project::factory()->raw(['name' => ''])],
            'missing status' => ['project' => Project::factory()->raw(['status' => null])],
        ];
    }

    // EDIT
    public function test_show_edit_project_page_requires_authentication()
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.edit', $project));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_projects_edit_page_with_any_project()
    {
        $project = Project::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->admin)->get(route('projects.edit', $project));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page
            ->component('Project/Edit')
            ->has('project.data', fn(Assert $page) => $page->where('id', $project->id)->etc())
        );
    }

    public function test_user_can_access_projects_edit_page_with_personal_project_only()
    {
        $personalProject = Project::factory()->create([
            'user_id' => $this->user->id
        ]);
        $otherPersonProject = Project::factory()->create();

        $response = $this->actingAs($this->user)->get(route('projects.edit', $personalProject));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page
            ->component('Project/Edit')
            ->has('project.data', fn(Assert $page) => $page->where('id', $personalProject->id)->etc())
        );

        $response = $this->actingAs($this->user)->get(route('projects.edit', $otherPersonProject));
        $response->assertForbidden();
    }

    // UPDATE


    // DESTROY

}
