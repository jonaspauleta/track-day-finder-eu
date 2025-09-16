<?php

declare(strict_types=1);

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Event extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Event>
     */
    public static $model = \App\Models\Event::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title', 'description',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            Textarea::make('Description')
                ->nullable()
                ->rules('nullable', 'max:1000'),

            BelongsTo::make('Track', 'track', Track::class)
                ->searchable()
                ->sortable()
                ->rules('required'),

            BelongsTo::make('Organizer', 'organizer', Organizer::class)
                ->searchable()
                ->sortable()
                ->rules('required'),

            Date::make('Start Date', 'start_date')
                ->sortable()
                ->rules('required', 'date', 'before_or_equal:end_date'),

            Date::make('End Date', 'end_date')
                ->sortable()
                ->rules('required', 'date', 'after_or_equal:start_date'),

            URL::make('Website')
                ->nullable()
                ->rules('nullable', 'url'),
        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
