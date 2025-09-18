<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TracksController extends Controller
{
    public function index(Request $request): Response
    {
        $tracks = Track::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'country',
                'city',
                'website',
                'noise_limit',
            ]);

        return Inertia::render('tracks/index', [
            'tracks' => $tracks,
        ]);
    }
}


