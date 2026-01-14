<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_be_soft_deleted()
    {
        User::factory()->count(3)->create();

        User::query()->delete();

        $this->assertDatabaseCount('users', 3);
        $this->assertCount(0, User::all());
        $this->assertCount(3, User::withTrashed()->get());
    }
}
