<?php

declare(strict_types=1);

use App\Models\Track;
use App\Models\User;

it('shows tracks index', function () {
    $user = User::factory()->create();
    Track::factory()->count(3)->create();

    $this->actingAs($user)
        ->get('/tracks')
        ->assertSuccessful();
});


