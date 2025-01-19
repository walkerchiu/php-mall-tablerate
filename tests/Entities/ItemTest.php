<?php

namespace WalkerChiu\MallTableRate;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\MallTableRate\Models\Entities\Setting;
use WalkerChiu\MallTableRate\Models\Entities\Item;
use WalkerChiu\MallTableRate\Models\Entities\ItemLang;

class ItemTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\MallTableRate\MallTableRateServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Item.
     *
     * For WalkerChiu\MallTableRate\Models\Entities\Item
     *
     * @return void
     */
    public function testItem()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-mall-tablerate.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-mall-tablerate.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-mall-tablerate.soft_delete', 1);

        $faker = \Faker\Factory::create();

        $db_setting = factory(Setting::class)->create();

        // Give
        $db_morph_1 = factory(Item::class)->create(['setting_id' => $db_setting->id]);
        $db_morph_2 = factory(Item::class)->create(['setting_id' => $db_setting->id]);
        $db_morph_3 = factory(Item::class)->create(['setting_id' => $db_setting->id]);

        // Get records after creation
            // When
            $records = Item::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $db_morph_2->delete();
            $records = Item::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Item::withTrashed()
                ->find($db_morph_2->id)
                ->restore();
            $record_2 = Item::find($db_morph_2->id);
            $records = Item::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);
    }
}
