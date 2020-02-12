<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Dababo\LazyChunk\Tests\Models\Article::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
    ];
});