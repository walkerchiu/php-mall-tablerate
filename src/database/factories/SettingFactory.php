<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MallTableRate\Models\Entities\Setting;
use WalkerChiu\MallTableRate\Models\Entities\SettingLang;

$factory->define(Setting::class, function (Faker $faker) {
    return [
        'type'       => $faker->slug,
        'identifier' => $faker->slug
    ];
});

$factory->define(SettingLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
