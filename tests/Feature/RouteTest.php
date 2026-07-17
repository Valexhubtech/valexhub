<?php

use App\Models\User;

it('responds successfully for all public routes', function (string $route) {
    $this->followingRedirects()->get($route)->assertSuccessful();
})->with('routes');

test('responds successfully for all auth routes', function ($url) {
    $user = User::find(1);

    $this->actingAs($user);

    $this->followingRedirects()->get($url)->assertSuccessful();
})->with('authroutes');
