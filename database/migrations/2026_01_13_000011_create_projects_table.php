<?php

use App\Enums\ProjectStatusEnum;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('status')->default(ProjectStatusEnum::Pending->value);
            $table->string('file')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
