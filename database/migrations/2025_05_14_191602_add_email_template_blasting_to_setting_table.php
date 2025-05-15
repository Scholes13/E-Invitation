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
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'email_template_blasting')) {
                $table->longText('email_template_blasting')->nullable()->after('blasting_email');
            }
            
            if (!Schema::hasColumn('setting', 'email_subject_template')) {
                $table->string('email_subject_template')->nullable()->after('email_template_blasting');
            }
            
            if (!Schema::hasColumn('setting', 'whatsapp_template_blasting')) {
                $table->longText('whatsapp_template_blasting')->nullable()->after('email_subject_template');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'email_template_blasting')) {
                $table->dropColumn('email_template_blasting');
            }
            
            if (Schema::hasColumn('setting', 'email_subject_template')) {
                $table->dropColumn('email_subject_template');
            }
            
            if (Schema::hasColumn('setting', 'whatsapp_template_blasting')) {
                $table->dropColumn('whatsapp_template_blasting');
            }
        });
    }
};
