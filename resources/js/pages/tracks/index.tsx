import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { Head, Link, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { type BreadcrumbItem, type SharedData } from '@/types';

type Track = {
    id: number;
    name: string;
    country: string | null;
    city: string | null;
    website: string | null;
    noise_limit: string | null;
};

interface PageProps extends SharedData {
    tracks: Track[];
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Tracks', href: '/tracks' },
];

export default function TracksIndex() {
    const { props } = usePage<PageProps>();
    const [query, setQuery] = useState('');

    const filtered = useMemo(() => {
        const q = query.trim().toLowerCase();
        if (!q) return props.tracks;
        return props.tracks.filter((t) =>
            [t.name, t.city ?? '', t.country ?? ''].some((v) => v.toLowerCase().includes(q))
        );
    }, [props.tracks, query]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tracks" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between gap-4">
                    <h1 className="text-xl font-semibold">Tracks</h1>
                    <div className="w-64">
                        <Input
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            placeholder="Search by name, city, country"
                        />
                    </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {filtered.map((track) => (
                        <Card key={track.id} className="h-full">
                            <CardHeader>
                                <CardTitle className="truncate" title={track.name}>{track.name}</CardTitle>
                            </CardHeader>
                            <CardContent className="flex flex-col gap-2 text-sm">
                                <div className="text-muted-foreground">
                                    {(track.city || track.country) ? [track.city, track.country].filter(Boolean).join(', ') : '—'}
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="text-muted-foreground">Noise limit</div>
                                    <div>{track.noise_limit ?? '—'}</div>
                                </div>
                                {track.website && (
                                    <a href={track.website} target="_blank" rel="noreferrer" className="text-primary underline">
                                        Website
                                    </a>
                                )}
                            </CardContent>
                        </Card>
                    ))}
                    {filtered.length === 0 && (
                        <div className="col-span-full grid place-items-center rounded-lg border p-8 text-center text-sm text-muted-foreground">
                            No tracks found.
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}


