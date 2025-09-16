<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Organizer;

it('can create an organizer using factory', function () {
    $organizer = Organizer::factory()->create();

    expect($organizer)->toBeInstanceOf(Organizer::class);
    expect($organizer->name)->toBeString();
    expect($organizer->email)->toBeString()->or->toBeNull();
    expect($organizer->website)->toBeString()->or->toBeNull();
    expect($organizer->logo_url)->toBeString()->or->toBeNull();
    expect($organizer->created_at)->not->toBeNull();
    expect($organizer->updated_at)->not->toBeNull();
});

it('can create an organizer with specific attributes', function () {
    $organizer = Organizer::factory()->create([
        'name' => 'Track Day Events Ltd',
        'email' => 'info@trackdayevents.com',
        'website' => 'https://www.trackdayevents.com',
        'logo_url' => 'https://example.com/logo.png',
    ]);

    expect($organizer->name)->toBe('Track Day Events Ltd');
    expect($organizer->email)->toBe('info@trackdayevents.com');
    expect($organizer->website)->toBe('https://www.trackdayevents.com');
    expect($organizer->logo_url)->toBe('https://example.com/logo.png');
});

it('can create an organizer with only required fields', function () {
    $organizer = Organizer::factory()->create([
        'name' => 'Minimal Organizer',
        'email' => null,
        'website' => null,
        'logo_url' => null,
    ]);

    expect($organizer->name)->toBe('Minimal Organizer');
    expect($organizer->email)->toBeNull();
    expect($organizer->website)->toBeNull();
    expect($organizer->logo_url)->toBeNull();
});

it('has many events relationship', function () {
    $organizer = Organizer::factory()->create();

    expect($organizer->events())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('can have multiple events', function () {
    $organizer = Organizer::factory()->create();
    $events = Event::factory()->count(4)->create(['organizer_id' => $organizer->id]);

    expect($organizer->events)->toHaveCount(4);
    expect($organizer->events->first())->toBeInstanceOf(Event::class);
    expect($organizer->events->pluck('id'))->toEqual($events->pluck('id'));
});

it('can create multiple organizers with unique data', function () {
    $organizers = Organizer::factory()->count(3)->create();

    expect($organizers)->toHaveCount(3);
    $organizers->each(function ($organizer) {
        expect($organizer)->toBeInstanceOf(Organizer::class);
        expect($organizer->name)->toBeString();
    });
});

it('persists to database correctly', function () {
    $organizer = Organizer::factory()->create();

    $this->assertDatabaseHas('organizers', [
        'id' => $organizer->id,
        'name' => $organizer->name,
        'email' => $organizer->email,
        'website' => $organizer->website,
        'logo_url' => $organizer->logo_url,
    ]);
});

it('can handle null optional fields in database', function () {
    $organizer = Organizer::factory()->create([
        'email' => null,
        'website' => null,
        'logo_url' => null,
    ]);

    $this->assertDatabaseHas('organizers', [
        'id' => $organizer->id,
        'name' => $organizer->name,
        'email' => null,
        'website' => null,
        'logo_url' => null,
    ]);
});
