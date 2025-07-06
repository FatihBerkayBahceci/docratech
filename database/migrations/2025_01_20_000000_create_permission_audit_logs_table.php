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
        Schema::create('permission_audit_logs', function (Blueprint $table) {
            $table->id();
            
            // HIPAA Compliance Fields
            $table->string('event_type')->index(); // create, update, delete, assign, revoke
            $table->string('action')->index(); // specific action performed
            $table->string('resource_type')->index(); // permission, role, user_permission
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('resource_name')->nullable();
            
            // User Information
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_role')->nullable();
            
            // Subject Information (affected user/role)
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable(); // user, role
            $table->string('subject_name')->nullable();
            
            // Permission Details
            $table->unsignedBigInteger('permission_id')->nullable();
            $table->string('permission_name')->nullable();
            $table->string('permission_module')->nullable();
            
            // Change Details
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            
            // Request Information
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('request_id')->nullable();
            
            // Compliance Fields
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->boolean('requires_attention')->default(false);
            $table->timestamp('retention_until')->nullable();
            
            // Immutable timestamp
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
            
            // Indexes for performance and compliance queries
            $table->index(['event_type', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['permission_id', 'occurred_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['ip_address', 'occurred_at']);
            $table->index(['severity', 'requires_attention']);
            $table->index('retention_until');
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_audit_logs');
    }
}; 