<?php

namespace Database\Factories;

use App\Enums\ProjectStatusEnum;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->text(),
            'status' => ProjectStatusEnum::Pending->value,
            'deadline' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
            'file' => UploadedFile::fake()->image($this->faker->word() . '.jpg'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
