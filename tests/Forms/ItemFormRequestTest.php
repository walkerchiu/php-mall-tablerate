<?php

namespace WalkerChiu\MallTableRate;

use Illuminate\Support\Facades\Validator;
use WalkerChiu\MallTableRate\Models\Entities\Setting;
use WalkerChiu\MallTableRate\Models\Forms\ItemFormRequest;

class ItemFormRequestTest extends \Orchestra\Testbench\TestCase
{
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

        $this->request  = new ItemFormRequest();
        $this->rules    = $this->request->rules();
        $this->messages = $this->request->messages();
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
     * Unit test about Authorize.
     *
     * For WalkerChiu\MallTableRate\Models\Forms\ItemFormRequest
     *
     * @return void
     */
    public function testAuthorize()
    {
        $this->assertEquals(true, 1);
    }

    /**
     * Unit test about Rules.
     *
     * For WalkerChiu\MallTableRate\Models\Forms\ItemFormRequest
     *
     * @return void
     */
    public function testRules()
    {
        $faker = \Faker\Factory::create();

        $db_setting = factory(Setting::class)->create();

        // Give
        $attributes = [
            'setting_id' => $db_setting->id,
            'area'       => 'TWN',
            'attribute'  => $faker->slug,
            'min'        => 1,
            'operator'   => '=',
            'value'      => 1
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(false, $fails);

        // Give
        $attributes = [
            'setting_id' => $db_setting->id,
            'area'       => 'TWN',
            'attribute'  => $faker->slug,
            'min'        => 1,
            'operator'   => '='
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);
    }
}
