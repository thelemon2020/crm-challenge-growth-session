<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Project;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use LazilyRefreshDatabase;

    /*
        test('can create project with valid data')
        test('project belongs to client')
        test('project belongs to user')
        test('project has many tasks')
        test('project can have media attachments')
        test('date accessors return formatted dates')
        test('in_progress scope filters correctly')
    */

    // we can upload many files to either a project or a task

    public function test_project_has_many_files()
    {
        $project = Project::factory()
            ->hasFiles(3)
            ->create();

        $this->assertCount(3, $project->files);
    }

    public function test_task_has_many_files()
    {
        $this->markTestSkipped();
    }
}
