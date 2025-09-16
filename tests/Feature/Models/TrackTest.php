<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Track;

it('can create a track using factory', function () {
    $track = Track::factory()->create();

    expect($track)->toBeInstanceOf(Track::class);
    expect($track->name)->toBeString();
    expect($track->country)->toBeString();
    expect($track->city)->toBeString();
    expect($track->latitude)->toBeFloat();
    expect($track->longitude)->toBeFloat();
    expect($track->website)->toBeString()->or->toBeNull();
    if ($track->noise_limit !== null) {
        expect($track->noise_limit)->toBeInt();
    } else {
        expect($track->noise_limit)->toBeNull();
    }
    expect($track->created_at)->not->toBeNull();
    expect($track->updated_at)->not->toBeNull();
});

it('can create a track with specific attributes', function () {
    $track = Track::factory()->create([
        'name' => 'Silverstone Circuit',
        'country' => 'United Kingdom',
        'city' => 'Silverstone',
        'latitude' => 52.0786,
        'longitude' => -1.0169,
        'website' => 'https://www.silverstone.co.uk',
        'noise_limit' => 105,
    ]);

    expect($track->name)->toBe('Silverstone Circuit');
    expect($track->country)->toBe('United Kingdom');
    expect($track->city)->toBe('Silverstone');
    expect($track->latitude)->toBe(52.0786);
    expect($track->longitude)->toBe(-1.0169);
    expect($track->website)->toBe('https://www.silverstone.co.uk');
    expect($track->noise_limit)->toBe(105);
});

it('can create a track without optional fields', function () {
    $track = Track::factory()->create([
        'website' => null,
        'noise_limit' => null,
    ]);

    expect($track->website)->toBeNull();
    expect($track->noise_limit)->toBeNull();
});

it('has many events relationship', function () {
    $track = Track::factory()->create();

    expect($track->events())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('can have multiple events', function () {
    $track = Track::factory()->create();
    $events = Event::factory()->count(3)->create(['track_id' => $track->id]);

    expect($track->events)->toHaveCount(3);
    expect($track->events->first())->toBeInstanceOf(Event::class);
    expect($track->events->pluck('id'))->toEqual($events->pluck('id'));
});

it('validates latitude and longitude precision', function () {
    $track = Track::factory()->create([
        'latitude' => 52.0786123,
        'longitude' => -1.0169456,
    ]);

    // The database should store the values with proper precision
    expect($track->latitude)->toBe(52.0786123);
    expect($track->longitude)->toBe(-1.0169456);
});

it('can create multiple tracks with unique data', function () {
    $tracks = Track::factory()->count(5)->create();

    expect($tracks)->toHaveCount(5);
    $tracks->each(function ($track) {
        expect($track)->toBeInstanceOf(Track::class);
        expect($track->name)->toBeString();
        expect($track->country)->toBeString();
        expect($track->city)->toBeString();
    });
});

it('persists to database correctly', function () {
    $track = Track::factory()->create();

    $this->assertDatabaseHas('tracks', [
        'id' => $track->id,
        'name' => $track->name,
        'country' => $track->country,
        'city' => $track->city,
        'latitude' => $track->latitude,
        'longitude' => $track->longitude,
    ]);
});
