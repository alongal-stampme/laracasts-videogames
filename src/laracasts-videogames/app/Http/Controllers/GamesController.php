<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current = Carbon::now()->timestamp;
        $afterFourMonths = Carbon::now()->addMonths(4)->timestamp;

        $mostAnticipated = [];
        /*
        $mostAnticipated = Http::withHeaders(config('services.igdb'))
            ->withOptions([
                'body' => "
                    fields name, cover.url, first_release_date, popularity,
                    platforms.abbreviation, rating, rating_count, summary;
                    where platforms = (48,49,130,6)
                    & (first_release_date >= {$current}
                    & first_release_date <  {$afterFourMonths});
                    sort popularity desc;
                    limit 4;
                "
            ])
            ->get('https://api-v3.igdb.com/games/')
            ->json();
        */

//        dump($mostAnticipated);

        $comingSoon = [];
        /*
        $comingSoon = Http::withHeaders(config('services.igdb'))
            ->withOptions([
                'body' => "
                    fields name, cover.url, first_release_date, popularity,
                    platforms.abbreviation, rating, rating_count, summary;
                    where platforms = (48,49,130,6)
                    & (first_release_date >= {$current}
                    & popularity > 5);
                    sort first_release_date asc;
                    limit 4;
                "
            ])
            ->get('https://api-v3.igdb.com/games/')
            ->json();
        */

//        dump($comingSoon);

        return view('index', [
            'mostAnticipated' => $mostAnticipated,
            'comingSoon' => $comingSoon,
        ]);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function show($slug)
    {
        $game = Http::withHeaders(config('services.igdb'))
            ->withOptions([
                'body' => "
                    fields name, cover.url, first_release_date, popularity,
                    platforms.abbreviation, rating, slug,
                    involved_companies.company.name, genres.name,
                    aggregated_rating, summary, websites.*, videos.*,
                    screenshots.*, similar_games.cover.url,
                    similar_games.name, similar_games.rating,
                    similar_games.platforms.abbreviation, similar_games.slug;
                    where slug=\"{$slug}\";
                "
            ])
            ->get('https://api-v3.igdb.com/games/')
            ->json();

        abort_if(! $game, 404);

        return view('show', [
            'game' => $this->formatGameForView($game[0]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function formatGameForView($game)
    {
        return collect($game)->merge([
            'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
            'genres' => collect($game['genres'])->pluck('name')->implode(', '),
            'involvedCompanies' => $game['involved_companies'][0]['company']['name'],
            'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            'memberRating' => isset($game['rating']) ? round($game['rating'] . '%') : '0%',
            'criticRating' => isset($game['aggregated_rating']) ? round($game['aggregated_rating'] . '%') : '0%',
            'trailer' => "https://youtube.com/watch/{$game['videos'][0]['video_id']}",
            'screenshots' => collect($game['screenshots'])->map(function ($screenshot) {
                return [
                    'big' => Str::replaceFirst('thumb', 'screenshot_big', $screenshot['url']),
                    'huge' => Str::replaceFirst('thumb', 'screenshot_huge', $screenshot['url']),
                ];
            })->take(9),
            'similarGames' => collect($game['similar_games'])->map(function ($game) {
                return collect($game)->merge([
                    'coverImageUrl' => isset($game['cover'])
                        ? Str::replaceFirst('thumb', 'cover_big', $game['cover']['url'])
                        : 'https://via.placeholder.com/264x352',
                    'rating' => isset($game['rating']) ? round($game['rating']) : null,
                    'platforms' => isset($game['platforms'])
                        ? collect($game['platforms'])->pluck('abbreviation')->implode(', ')
                        : null
                ]);
            })->take(6),
            'social' => [
                'website' => collect($game['websites'])->first(),
                'facebook' => collect($game['websites'])->filter(function ($website) {
                    return Str::contains($website['url'], 'facebook');
                })->first(),
                'twitter' => collect($game['websites'])->filter(function ($website) {
                    return Str::contains($website['url'], 'twitter');
                })->first(),
                'instagram' => collect($game['websites'])->filter(function ($website) {
                    return Str::contains($website['url'], 'instagram');
                })->first(),
            ]
        ]);
    }
}
