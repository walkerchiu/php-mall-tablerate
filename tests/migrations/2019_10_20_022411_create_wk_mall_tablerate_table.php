<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkMallTableRateTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.mall-tablerate.settings'), function (Blueprint $table) {
            $table->uuid('id');
            $table->nullableUuidMorphs('host');
            $table->string('type');
            $table->string('serial')->nullable();
            $table->string('identifier')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->primary('id');
            $table->index('type');
            $table->index('identifier');
            $table->index('is_enabled');
        });
        if (!config('wk-mall-tablerate.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.mall-tablerate.settings_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->text('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }

        Schema::create(config('wk-core.table.mall-tablerate.items'), function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('setting_id');
            $table->string('area');
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('attribute');
            $table->unsignedDecimal('min');
            $table->unsignedDecimal('max')->nullable();
            $table->string('operator');
            $table->string('value');

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('setting_id')->references('id')
                  ->on(config('wk-core.table.mall-tablerate.settings'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->primary('id');
        });
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.mall-tablerate.items'));
        Schema::dropIfExists(config('wk-core.table.mall-tablerate.settings_lang'));
        Schema::dropIfExists(config('wk-core.table.mall-tablerate.settings'));
    }
}
