<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Attachments;
use App\Channel;
use App\Reply;
use App\User;
use App\Thread;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,

        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'avatar_path' => 'images/avatars/default-avatar.png',
        'confirmed' => true

    ];
});

$factory->state(App\User::class, 'administrator', function () {
    return [
        'name' => 'JohnDoe'
    ];
});


$factory->define(Channel::class, function (Faker $faker) {

    $name = $faker->word();
    return [


        'name' => $name,
        'slug' => $name
    ];
});

$factory->define(Thread::class, function (Faker $faker) {
    $title = $faker->sentence;
    return [
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        'channel_id' => function () {
            return factory('App\Channel')->create()->id;
        },

        'title' => $title,
        'slug' => str_slug($title),
        'body' => $faker->paragraph,
        'locked' => false,
    ];
});




$factory->define(Reply::class, function (Faker $faker) {
    return [
        'thread_id' => function () {
            return factory('App\Thread')->create()->id;
        },

        'user_id' => function () {
            return factory('App\User')->create()->id;
        },

        'body' => $faker->paragraph,
        'attachments_count' => 0,
    ];
});


$factory->define(DatabaseNotification::class, function (Faker $faker) {
    return [

        'id' => Uuid::uuid4()->toString(),

        'type' => 'App\Notifications\ThreadWasUpdated',

        'notifiable_type' => 'App\User',

        'notifiable_id' => function () {

            return auth()->id() ?: factory('App\User')->create()->id;
        },

        'data' => ['message' => 'temporary notification']

    ];
});

