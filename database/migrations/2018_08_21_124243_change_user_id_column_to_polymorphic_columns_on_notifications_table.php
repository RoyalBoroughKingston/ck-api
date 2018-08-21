<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserIdColumnToPolymorphicColumnsOnNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->string('notifiable_type')->nullable()->after('id');
            $table->uuid('notifiable_id')->nullable()->after('notifiable_type');
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn('notifiable_type');
            $table->dropColumn('notifiable_id');

            $table->uuid('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
