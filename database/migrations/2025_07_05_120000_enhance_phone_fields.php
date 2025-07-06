<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add enterprise phone features
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Keep existing phone field as is, add new enterprise fields
            $table->string('phone_country', 2)->nullable()->after('phone'); // ISO country code
            $table->string('phone_e164', 20)->nullable()->after('phone_country'); // E.164 format
            $table->enum('phone_type', ['mobile', 'landline', 'toll_free', 'premium', 'unknown'])->nullable()->after('phone_e164');
            $table->boolean('phone_verified')->default(false)->after('phone_type');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_verified');
            $table->string('phone_verification_code', 6)->nullable()->after('phone_verified_at');
            $table->timestamp('phone_verification_expires_at')->nullable()->after('phone_verification_code');
            $table->json('phone_metadata')->nullable()->after('phone_verification_expires_at'); // Carrier, region, etc.
            
            // Indexes for performance
            $table->index(['phone_e164', 'phone_verified']);
            $table->index('phone_country');
            $table->index('phone_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone_e164', 'phone_verified']);
            $table->dropIndex(['phone_country']);
            $table->dropIndex(['phone_type']);
            
            $table->dropColumn([
                'phone_country',
                'phone_e164',
                'phone_type',
                'phone_verified',
                'phone_verified_at',
                'phone_verification_code',
                'phone_verification_expires_at',
                'phone_metadata'
            ]);
        });
    }
}; 