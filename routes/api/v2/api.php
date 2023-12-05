<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

use App\Http\Controllers\Api\V2\ApiController;

Route::get('status', function(Request $request){
    return array(
		'status' => true,
		'version' => '2.0',
	);
});

Route::prefix('home')->name('home.')->group(function () {
	Route::get('episodesRecents', [ApiController::class, 'episodesRecents']);
	Route::get('animesPopulars', [ApiController::class, 'animesPopulars']);
	Route::get('animesLatinos', [ApiController::class, 'animesLatinos']);
});

Route::get('/home',[ApiController::class, 'getDataHome']);

Route::get('/animes/search/{search}',[ApiController::class, 'getAnimesSearch']);
Route::get('/animes/list',[ApiController::class, 'getAnimesList']);
Route::get('/anime/{anime_slug}',[ApiController::class, 'getDataAnime']);
Route::get('/anime/{anime_slug}/episodes',[ApiController::class, 'getEpisodesAnime']);
Route::get('/anime/{anime_slug}/episodes/{episode_number}/players',[ApiController::class, 'getPlayersEpisode']);
