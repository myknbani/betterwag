<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(Role::External);
        });
        DB::statement("
            ALTER TABLE users ADD CONSTRAINT check_shelter_role CHECK (
                (role = 'shelter_manager' AND shelter_id IS NOT NULL)
                OR
                (role <> 'shelter_manager' AND shelter_id IS NULL)
            )
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT check_shelter_role');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
