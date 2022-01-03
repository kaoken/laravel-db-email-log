<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable('logs') ) {
            Schema::create('logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamp('create_tm')->useCurrent();
                $table->integer('pid');
                $table->ipAddress('ip');
                $table->integer('level');
                $table->string('level_name',16);
                $table->text('route');
                $table->string('method',16);
                $table->string('user_agent',512);
                $table->mediumText('message')->nullable();
                $table->mediumText('context')->nullable();
            });

            switch (strtolower(app()['config']["database.default"]))
            {
                case 'mysql':
                    DB::statement("ALTER TABLE `".DB::getTablePrefix()."logs` MODIFY COLUMN `create_tm`  timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)");
                    break;
                case 'pgsql':
                    DB::statement("ALTER TABLE ".DB::getTablePrefix()."logs ALTER COLUMN create_tm TYPE timestamp(6) USING create_tm::timestamp");
                    break;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
