<?php

namespace Feature\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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
            ->component('Projects/Index')
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
            ->component('Projects/Index')
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
                ->component('Projects/Create'));
    }

    public function test_user_can_show_create_project_page()
    {
        $response = $this->actingAs($this->user)->get(route('projects.create'));

        $response->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Projects/Create'));
    }

    // STORE
    public function test_create_project_requires_authentication()
    {
        $response = $this->post(route('projects.store'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_create_project_with_valid_data()
    {
        $project = Project::factory()->make();
        $project->deadline = '2026-02-24 03:45:20';

        $response = $this->actingAs($this->admin)->post(route('projects.store'), $project->toArray());

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseCount('projects', 1);

        $this->assertDatabaseHas('projects', $project->except(['deadline', 'created_at', 'updated_at', 'file']));
    }

    public function test_user_can_create_project_with_valid_data()
    {
        $project = Project::factory()->make();
        $project['deadline'] = '2026-02-24 03:45:20';

        $response = $this->actingAs($this->user)->post(route('projects.store'), $project->toArray());

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseCount('projects', 1);
        $this->assertDatabaseHas('projects', $project->except(['file']));
    }

    public function test_cannot_create_project_with_invalid_deadline()
    {
        $project = Project::factory()->make([
            'deadline' => Carbon::now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)->post(route('projects.store'), $project->toArray());

        $response->assertRedirectBack(route('projects.create'));
        $response->assertSessionHasErrors('deadline');
        $this->assertDatabaseMissing('projects', $project->toArray());
    }

    public function test_user_can_upload_a_single_file_when_creating_a_new_project()
    {
        Storage::fake();

        $uploadedFile = UploadedFile::fake()->image('photo1.jpg');

        $project = Project::factory()->raw([
            'file' => $uploadedFile
        ]);
        $project['deadline'] = '2026-02-24 03:45:20';

        $response = $this->actingAs($this->user)->post(route('projects.store'), $project);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseCount('projects', 1);
        $this->assertDatabaseHas('projects', [
            ...$project,
            'file' => $uploadedFile->getClientOriginalName()
        ]);

        Storage::disk()->assertExists($uploadedFile->name);
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
            ->component('Projects/Edit')
            ->has('project.data', fn(Assert $page) => $page->where('id', $project->id)->etc())
        );
    }

    // UPDATE
    public function test_update_projects_requires_authentication()
    {
        // Arrange
        $projects = Project::factory()->count(3)->create();

        // Act
        $response = $this->put(route('projects.update', $projects[0]));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_update_project()
    {
        // Arrange
        Project::factory()->count(3)->create();
        $firstProject = Project::query()->first();

        // Act
        $response = $this->actingAs($this->user)->put(route('projects.update', $firstProject), [...$firstProject->toArray(), 'title' => 'updated title']);

        // Assert
        $response->assertRedirect(route('projects.index'));
        $firstProject->refresh();
        $this->assertEquals('updated title', $firstProject->title);
    }

    // DESTROY
    public function test_delete_projects_requires_authentication()
    {
        // Arrange
        $projects = Project::factory()->count(3)->create();

        // Act
        $response = $this->delete(route('projects.destroy', $projects->first()));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_delete_project()
    {
        // Arrange
        [$otherPeopleProject, ,] = Project::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->user)->delete(route('projects.destroy', $otherPeopleProject));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_delete_project()
    {
        // Arrange
        [$otherPeopleProject, ,] = Project::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->admin)->delete(route('projects.destroy', $otherPeopleProject));

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', $otherPeopleProject->toArray());
    }

    // test('can upload media to project')
}
