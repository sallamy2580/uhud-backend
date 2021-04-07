<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailVerificationExpiredAtIsEmailVerifiedFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verification_expired_at')) {
                $table->timestamp('email_verification_expired_at')->nullable()->after('forget_pass_expire_at');
            }
            if (!Schema::hasColumn('users', 'is_email_verified')) {
                $table->tinyInteger('is_email_verified')->default(0)->after('forget_pass_expire_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_email_verified')) {
                $table->dropColumn('is_email_verified');
            }
            if (Schema::hasColumn('users', 'email_verification_expired_at')) {
                $table->dropColumn('email_verification_expired_at');
            }
        });
    }
}
