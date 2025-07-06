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
            // Ana tabloda olmayan ek alanlar
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('profile_photo')->nullable();
            $table->json('education')->nullable();
            $table->json('specialties')->nullable();
            $table->json('certificates')->nullable();
            $table->json('work_experience')->nullable();
            $table->json('languages')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('website')->nullable();
            $table->json('publications')->nullable();
            $table->json('awards')->nullable();
            $table->json('references')->nullable();
            $table->json('insurances')->nullable();
            $table->json('calendar_settings')->nullable();
            $table->boolean('kvkk_approved')->default(false);
            $table->boolean('profile_public')->default(true);
            $table->text('admin_notes')->nullable();
            $table->json('documents')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address', 'city', 'country', 'profile_photo',
                'education', 'specialties', 'certificates', 'work_experience',
                'languages', 'linkedin', 'twitter', 'facebook', 'instagram', 'website',
                'publications', 'awards', 'references', 'insurances', 'calendar_settings',
                'kvkk_approved', 'profile_public', 'admin_notes', 'documents'
            ]);
        });
    }
};
