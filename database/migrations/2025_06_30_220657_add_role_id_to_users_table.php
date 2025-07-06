<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->boolean('email_verified')->default(false)->after('role_id');
            $table->string('last_login_ip')->nullable()->after('email_verified');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->index(['role_id']);
            $table->index(['email_verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['email_verified']);
            $table->dropColumn([
                'role_id', 'email_verified',
                'last_login_ip', 'failed_login_attempts', 'locked_until'
            ]);
        });
    }
};
