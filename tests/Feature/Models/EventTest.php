<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Track;

it('can create an event using factory', function () {
    $event = Event::factory()->create();

    expect($event)->toBeInstanceOf(Event::class);
    expect($event->track_id)->toBeInt();
    expect($event->organizer_id)->toBeInt();
    expect($event->title)->toBeString();
    expect($event->description)->toBeString()->or->toBeNull();
    expect($event->start_date)->toBeString();
    expect($event->end_date)->toBeString();
    expect($event->website)->toBeString()->or->toBeNull();
    expect($event->created_at)->not->toBeNull();
    expect($event->updated_at)->not->toBeNull();
});

it('can create an event with specific attributes', function () {
    $track = Track::factory()->create();
    $organizer = Organizer::factory()->create();

    $event = Event::factory()->create([
        'track_id' => $track->id,
        'organizer_id' => $organizer->id,
        'title' => 'Advanced Track Day',
        'description' => 'High-performance track day for experienced drivers',
        'start_date' => '2025-10-15',
        'end_date' => '2025-10-15',
        'website' => 'https://www.trackday.com/advanced',
    ]);

    expect($event->track_id)->toBe($track->id);
    expect($event->organizer_id)->toBe($organizer->id);
    expect($event->title)->toBe('Advanced Track Day');
    expect($event->description)->toBe('High-performance track day for experienced drivers');
    expect($event->start_date)->toBe('2025-10-15');
    expect($event->end_date)->toBe('2025-10-15');
    expect($event->website)->toBe('https://www.trackday.com/advanced');
});

it('can create an event with optional fields as null', function () {
    $event = Event::factory()->create([
        'description' => null,
        'website' => null,
    ]);

    expect($event->description)->toBeNull();
    expect($event->website)->toBeNull();
});

it('belongs to a track', function () {
    $track = Track::factory()->create();
    $event = Event::factory()->create(['track_id' => $track->id]);

    expect($event->track())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($event->track)->toBeInstanceOf(Track::class);
    expect($event->track->id)->toBe($track->id);
    expect($event->track->name)->toBe($track->name);
});

it('belongs to an organizer', function () {
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);

    expect($event->organizer())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($event->organizer)->toBeInstanceOf(Organizer::class);
    expect($event->organizer->id)->toBe($organizer->id);
    expect($event->organizer->name)->toBe($organizer->name);
});

it('can create multiple events for the same track', function () {
    $track = Track::factory()->create();
    $events = Event::factory()->count(3)->create(['track_id' => $track->id]);

    expect($events)->toHaveCount(3);
    $events->each(function ($event) use ($track) {
        expect($event->track_id)->toBe($track->id);
        expect($event->track)->toBeInstanceOf(Track::class);
    });
});

it('can create multiple events for the same organizer', function () {
    $organizer = Organizer::factory()->create();
    $events = Event::factory()->count(2)->create(['organizer_id' => $organizer->id]);

    expect($events)->toHaveCount(2);
    $events->each(function ($event) use ($organizer) {
        expect($event->organizer_id)->toBe($organizer->id);
        expect($event->organizer)->toBeInstanceOf(Organizer::class);
    });
});

it('persists to database correctly', function () {
    $event = Event::factory()->create();

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'track_id' => $event->track_id,
        'organizer_id' => $event->organizer_id,
        'title' => $event->title,
        'description' => $event->description,
        'start_date' => $event->start_date,
        'end_date' => $event->end_date,
        'website' => $event->website,
    ]);
});

it('can handle date fields correctly', function () {
    $event = Event::factory()->create([
        'start_date' => '2025-12-01',
        'end_date' => '2025-12-03',
    ]);

    expect($event->start_date)->toBe('2025-12-01');
    expect($event->end_date)->toBe('2025-12-03');
});

it('creates related models when using factory defaults', function () {
    $event = Event::factory()->create();

    // The factory should create related Track and Organizer models
    expect($event->track)->toBeInstanceOf(Track::class);
    expect($event->organizer)->toBeInstanceOf(Organizer::class);

    // Check that the models exist in the database
    $this->assertDatabaseHas('tracks', ['id' => $event->track_id]);
    $this->assertDatabaseHas('organizers', ['id' => $event->organizer_id]);
});

it('can create events with multi-day duration', function () {
    $event = Event::factory()->create([
        'start_date' => '2025-08-15',
        'end_date' => '2025-08-17',
    ]);

    expect($event->start_date)->toBe('2025-08-15');
    expect($event->end_date)->toBe('2025-08-17');
});
