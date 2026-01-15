<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'deadline' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
