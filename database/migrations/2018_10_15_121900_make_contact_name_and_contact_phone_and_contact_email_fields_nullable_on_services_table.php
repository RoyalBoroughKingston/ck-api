<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeContactNameAndContactPhoneAndContactEmailFieldsNullableOnServicesTable extends Migration
{
    /**
     * MakeContactNameAndContactPhoneAndContactEmailFieldsNullableOnServicesTable constructor.
     */
    public function __construct()
    {
        register_enum_type();
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->change();
            $table->string('contact_phone')->nullable()->change();
            $table->string('contact_email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('contact_name')->nullable(false)->change();
            $table->string('contact_phone')->nullable(false)->change();
            $table->string('contact_email')->nullable(false)->change();
        });
    }
}
