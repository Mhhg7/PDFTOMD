<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Roles for every signed-in user, and the private sales catalog used for the
 * order sheets. Nothing here is ever read by the public website.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role', 20)->default('viewer')->after('password');
            $t->boolean('active')->default(true)->after('role');
        });
        DB::table('users')->update(['role' => 'admin']); // people who already had the website dashboard

        Schema::create('sales_companies', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('logo')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('sales_products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained('sales_companies')->restrictOnDelete();
            $t->string('brand_name');
            $t->string('active_ingredient')->nullable();
            $t->string('dose')->nullable();
            $t->string('dosage_form')->nullable();
            $t->decimal('price', 14, 2)->nullable();
            $t->text('notes')->nullable();
            $t->string('photo')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->index(['company_id', 'sort']);
        });

        Schema::create('sales_activity', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 40);
            $t->string('subject');
            $t->text('details')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_activity');
        Schema::dropIfExists('sales_products');
        Schema::dropIfExists('sales_companies');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['role', 'active']));
    }
};
