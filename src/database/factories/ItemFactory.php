<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use WalkerChiu\MallTableRate\Models\Entities\Item;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'setting_id' => 1,
        'area'       => $faker->slug,
        'attribute'  => $faker->slug,
        'min'        => 1,
        'operator'   => '=',
        'value'      => 1
    ];
});
