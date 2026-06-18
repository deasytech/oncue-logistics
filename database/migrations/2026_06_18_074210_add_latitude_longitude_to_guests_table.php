<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('guests', function (Blueprint $table) {
      // Add latitude and longitude if they don't exist
      if (!Schema::hasColumn('guests', 'latitude')) {
        $table->double('latitude', 10, 6)->nullable()->after('address');
      }
      if (!Schema::hasColumn('guests', 'longitude')) {
        $table->double('longitude', 10, 6)->nullable()->after('latitude');
      }
    });
  }

  public function down()
  {
    Schema::table('guests', function (Blueprint $table) {
      if (Schema::hasColumn('guests', 'longitude')) {
        $table->dropColumn('longitude');
      }
      if (Schema::hasColumn('guests', 'latitude')) {
        $table->dropColumn('latitude');
      }
    });
  }
};
