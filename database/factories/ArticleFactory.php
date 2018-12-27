<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Article::class, function (Faker $faker) {

    $updated_at = $faker->dateTimeThisMonth();
    $created_at = $faker->dateTimeThisMonth($updated_at);

    return [
        'title' => $faker->address,
        'content' => $faker->company,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
        'state' => 1,
        'read_count' => $faker->randomNumber(),
        'like_count' => $faker->randomNumber(),
        'reply_count' => $faker->randomNumber(),
    ];
});
