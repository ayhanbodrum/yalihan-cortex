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
        Schema::create('analytics_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_type', 50); // 'context7_health', 'git_activity', 'dev_velocity', 'code_quality'
            $table->string('metric_name', 100);
            $table->json('metric_data'); // Flexible JSON storage for metric values
            $table->decimal('metric_value', 10, 2)->nullable(); // Numerical representation for easy querying
            $table->string('source', 50); // 'git_hook', 'mcp_server', 'artisan_command', 'ci_cd'
            $table->string('severity', 20)->default('info'); // 'critical', 'warning', 'info', 'success'
            $table->timestamp('recorded_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['metric_type', 'recorded_at']);
            $table->index(['metric_name', 'recorded_at']);
            $table->index(['source', 'recorded_at']);
            $table->index('severity');
        });

        Schema::create('context7_compliance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('violation_type', 100);
            $table->text('violation_description');
            $table->string('file_path')->nullable();
            $table->integer('line_number')->nullable();
            $table->json('violation_context')->nullable(); // Code snippet, related rules
            $table->boolean('auto_fixed')->default(false);
            $table->text('fix_description')->nullable();
            $table->string('severity', 20); // 'critical', 'major', 'minor', 'info'
            $table->string('source', 50); // 'pre_commit', 'ci_cd', 'manual_check'
            $table->timestamp('detected_at');
            $table->timestamp('fixed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['violation_type', 'detected_at']);
            $table->index(['auto_fixed', 'detected_at']);
            $table->index(['severity', 'detected_at']);
            $table->index('file_path');
        });

        Schema::create('development_velocity_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('developer_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->integer('commits_count')->default(0);
            $table->integer('files_changed')->default(0);
            $table->integer('lines_added')->default(0);
            $table->integer('lines_deleted')->default(0);
            $table->decimal('code_quality_score', 5, 2)->nullable(); // 0-100 scale
            $table->integer('context7_violations')->default(0);
            $table->integer('auto_fixes_applied')->default(0);
            $table->decimal('test_coverage', 5, 2)->nullable(); // Percentage
            $table->json('feature_tags')->nullable(); // ['bug_fix', 'feature', 'refactor', etc.]
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->timestamps();

            // Indexes
            $table->index(['developer_name', 'period_start']);
            $table->index(['branch_name', 'period_start']);
            $table->index('period_start');
        });

        Schema::create('ai_learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_type', 50); // 'git_commit', 'violation_fix', 'idea_generation'
            $table->text('learning_data');
            $table->json('extracted_patterns')->nullable();
            $table->json('generated_insights')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable(); // AI confidence in learning
            $table->boolean('applied')->default(false); // Whether insights were applied
            $table->text('application_result')->nullable();
            $table->string('yalihan_bekci_version', 20)->default('1.0');
            $table->timestamp('learned_at');
            $table->timestamps();

            // Indexes
            $table->index(['session_type', 'learned_at']);
            $table->index(['applied', 'learned_at']);
            $table->index('confidence_score');
        });

        Schema::create('project_health_snapshots', function (Blueprint $table) {
            $table->id();
            $table->decimal('overall_health_score', 5, 2); // 0-100 overall project health
            $table->decimal('context7_compliance_score', 5, 2); // 0-100 Context7 compliance
            $table->decimal('code_quality_score', 5, 2); // 0-100 code quality
            $table->decimal('test_coverage_score', 5, 2)->nullable(); // 0-100 test coverage
            $table->decimal('performance_score', 5, 2)->nullable(); // 0-100 performance metrics
            $table->integer('active_violations')->default(0);
            $table->integer('critical_issues')->default(0);
            $table->integer('total_files')->default(0);
            $table->integer('total_lines')->default(0);
            $table->json('health_details')->nullable(); // Detailed breakdown
            $table->json('recommendations')->nullable(); // AI recommendations
            $table->timestamp('snapshot_at');
            $table->timestamps();

            // Indexes
            $table->index('snapshot_at');
            $table->index('overall_health_score');
            $table->index(['critical_issues', 'snapshot_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_health_snapshots');
        Schema::dropIfExists('ai_learning_sessions');
        Schema::dropIfExists('development_velocity_metrics');
        Schema::dropIfExists('context7_compliance_logs');
        Schema::dropIfExists('analytics_metrics');
    }
};
