<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTechnicalToTerminal extends Migration
{
    public function up()
    {
        // Rename tables
        Schema::rename('technical_report_files', 'terminal_report_files');
        Schema::rename('technical_reports', 'terminal_reports');
        Schema::rename('technical_report_researcher', 'terminal_report_researcher');

        // Rename columns in terminal_report_files
        Schema::table('terminal_report_files', function (Blueprint $table) {
            $table->renameColumn('technical_report_id', 'terminal_report_id');
        });

        // Rename columns in terminal_reports
        Schema::table('terminal_reports', function (Blueprint $table) {
            $table->renameColumn('related_title', 'related_title'); // No change needed
            $table->renameColumn('related_type', 'related_type'); // No change needed
            $table->renameColumn('related_id', 'related_id'); // No change needed
            $table->renameColumn('cooperating_agency_id', 'cooperating_agency_id'); // No change needed
            $table->renameColumn('funding_agency_id', 'funding_agency_id'); // No change needed
            $table->renameColumn('date_started', 'date_started'); // No change needed
            $table->renameColumn('date_ended', 'date_ended'); // No change needed
            $table->renameColumn('total_approved_budget', 'total_approved_budget'); // No change needed
            $table->renameColumn('actual_released_budget', 'actual_released_budget'); // No change needed
            $table->renameColumn('actual_expenditure', 'actual_expenditure'); // No change needed
            $table->renameColumn('abstract', 'abstract'); // No change needed
            $table->renameColumn('related_link', 'related_link'); // No change needed
            $table->renameColumn('review_status_id', 'review_status_id'); // No change needed
            $table->renameColumn('is_publish', 'is_publish'); // No change needed
            $table->renameColumn('created_at', 'created_at'); // No change needed
            $table->renameColumn('updated_at', 'updated_at'); // No change needed
        });

        // Rename columns in terminal_report_researcher
        Schema::table('terminal_report_researcher', function (Blueprint $table) {
            $table->renameColumn('technical_report_id', 'terminal_report_id');
        });
    }


    public function down()
    {
        // Rename tables back
        Schema::rename('terminal_report_files', 'technical_report_files');
        Schema::rename('terminal_reports', 'technical_reports');
        Schema::rename('terminal_report_researcher', 'technical_report_researcher');

        // Rename columns back in technical_report_files
        Schema::table('technical_report_files', function (Blueprint $table) {
            $table->renameColumn('terminal_report_id', 'technical_report_id');
        });

        // Rename columns back in technical_reports
        Schema::table('technical_reports', function (Blueprint $table) {
            // No changes needed as there were no renames
        });

        // Rename columns back in technical_report_researcher
        Schema::table('technical_report_researcher', function (Blueprint $table) {
            $table->renameColumn('terminal_report_id', 'technical_report_id');
        });
    }
}
