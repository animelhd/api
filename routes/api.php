<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\WebController;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Stream\StreamController;
use App\Http\Controllers\Import\Tio;
use App\Http\Controllers\Import\Jk;
use App\Http\Controllers\Import\Monos;
use App\Http\Controllers\Import\Fenix;
use App\Http\Controllers\Import\Sitemap;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Auth\RegisteredUserController;

// (Optional) if you want to upload episodes before 30min.
Route::prefix('import')->name('import.')->group(function () {
	Route::get('jk', [Jk::class, 'EpisodesImport']);
	Route::get('monos', [Monos::class, 'EpisodesImport']);
    Route::get('fenix', [Fenix::class, 'EpisodesImport']);
});

Route::get('/import/monos', [Monos::class, 'EpisodesImport']);

Route::get('/tio/{server}/{anime}/{inicio}/{fin}', [Tio::class, 'getLinksByServer']);

Route::get('/debug', [Tio::class, 'EpisodesImport'])->name('index');
Route::get('/sitemap', [ApiController::class, 'sitemap'])->name('sitemap');

Route::prefix('app')->name('app.')->group(function () {
	Route::get('recents', [ApiController::class, 'getRecenteApp']);
	Route::get('newApp', [ApiController::class, 'getNewApp']);
	Route::get('listApp', [ApiController::class, 'getListApp2']);
	Route::get('home', [ApiController::class, 'getDataHome']);
	Route::middleware(['throttle:app'])->group(function () {
		//Route::get('anime/list5', [ApiController::class, 'getAnimeList']);
		//Route::get('episode/list5', [ApiController::class, 'getEpisodeList']);
		//Route::get('server/list2', [ApiController::class, 'getServersList']);
		//Route::get('player/list5', [ApiController::class, 'getPlayerList']);
	});
	Route::middleware('appMd')->group(function () {
		Route::get('recentApp', [ApiController::class, 'getRecentApp']);
		Route::get('listApp2', [ApiController::class, 'getListApp']);
		Route::get('lastPlayer', [ApiController::class, 'getLastPlayer']);
	});
	// Authenticate User
	Route::prefix('auth')->name('auth.')->group(function () {
		Route::post('tokenApp', [ApiController::class, 'getTokenApp']);
		Route::post('registerApp', [ApiController::class, 'getRegister']);
		Route::post('forgotPassword', [ApiController::class, 'forgotPassword']);
		Route::middleware('appsanctum')->group(function () {
			Route::post('update', [UserController::class, 'updateProfile']);
			Route::get('user', [ApiController::class, 'loginUser']);
			Route::get('logout', [ApiController::class, 'logoutUser']);
			Route::prefix('favorite')->group(function () {
				Route::post('add', [ApiController::class, 'addAnimeFavorite']);
				Route::post('delete', [ApiController::class, 'deleteAnimeFavorite']);
				Route::post('list', [ApiController::class, 'listAnimeFavorite']);
			});
			Route::prefix('view')->group(function () {
				Route::post('add', [ApiController::class, 'addAnimeView']);
				Route::post('delete', [ApiController::class, 'deleteAnimeView']);
				Route::post('list', [ApiController::class, 'listAnimeView']);
			});	
			Route::prefix('watching')->group(function () {
				Route::post('add', [ApiController::class, 'addAnimeWatching']);
				Route::post('delete', [ApiController::class, 'deleteAnimeWatching']);
				Route::post('list', [ApiController::class, 'listAnimeWatching']);
			});			
		});
	});
	Route::get('anime/{anime_slug}', [ApiController::class, 'getDataAnime']);
	Route::prefix('server')->name('server.')->group(function () {
		//Route::get('{anime_id}/{episode_number}/{languaje}', [ApiController::class, 'getServerApp']);
	});
	Route::prefix('player')->name('players.')->group(function () {
		Route::get('lastplayer', [ApiController::class, 'getLastPlayer']);
		//Route::get('{player_id}', [ApiController::class, 'getPlayerApp']);
	});
	Route::get('view-anime/{id}/{episode_id}', [ApiController::class, 'setViewsAnime']);
	Route::get('view-animes/{id}', [ApiController::class, 'setViewsAnimes']);

});

// Routes appWeb
Route::get('releases', [ApiController::class, 'releases']);
Route::prefix('anime')->name('anime.')->group(function () {
	Route::get('latino', [ApiController::class, 'latino']);
	Route::get('latino2', [ApiController::class, 'latino2']);
	Route::get('trending', [ApiController::class, 'trending']);
	Route::get('more-view', [ApiController::class, 'moreView']);	
	Route::get('search', [ApiController::class, 'search']);
	Route::get('simulcast', [ApiController::class, 'simulcast']);
	Route::get('list', [ApiController::class, 'listAnimes']);
	Route::get('{anime_slug}', [ApiController::class, 'getAnime']);
	Route::get('{anime_slug}/episodes', [ApiController::class, 'getAnimeEpisodes']);
	Route::get('{anime_slug}/episodes/{episode_number}', [ApiController::class, 'getEpisodePlayers']);
	Route::get('{anime_slug}/recommendations', [ApiController::class, 'getAnimeRecommendations']);
});
Route::prefix('episodes')->name('episodes.')->group(function () {
	Route::get('{anime_slug}/{episode_number}', [ApiController::class, 'getEpisode']);
});
Route::prefix('players')->name('players.')->group(function () {
	Route::get('{id}', [StreamController::class, 'getVideoMp4']);
});
Route::get('filterings', [ApiController::class, 'filterings']);
// Route::fallback(function () {return redirect('404');});



// Routes Web V1
Route::prefix('web/v1')->group(function () {
	Route::get('home', [WebController::class, 'home']);
	Route::prefix('anime')->name('anime.')->group(function () {
		Route::get('latino', [ApiController::class, 'latino']);
		Route::get('latino2', [ApiController::class, 'latino2']);
		Route::get('trending', [ApiController::class, 'trending']);
		Route::get('more-view', [ApiController::class, 'moreView']);	
		Route::get('search', [ApiController::class, 'search']);
		Route::get('simulcast', [ApiController::class, 'simulcast']);
		Route::get('list', [ApiController::class, 'listAnimes']);
		Route::get('{anime_slug}', [ApiController::class, 'getAnime']);
		Route::get('{anime_slug}/episodes', [ApiController::class, 'getAnimeEpisodes']);
		Route::get('{anime_slug}/episodes/{episode_number}', [ApiController::class, 'getEpisodePlayers']);
		Route::get('{anime_slug}/recommendations', [ApiController::class, 'getAnimeRecommendations']);
	});
	Route::prefix('episodes')->name('episodes.')->group(function () {
		Route::get('{anime_slug}/{episode_number}', [ApiController::class, 'getEpisode']);
	});
	Route::prefix('players')->name('players.')->group(function () {
		Route::get('{id}', [StreamController::class, 'getVideoMp4']);
	});
	Route::get('filterings', [ApiController::class, 'filterings']);
	// Route::fallback(function () {return redirect('404');});
});



// Config

Route::get('config', [ApiController::class, 'config']);
Route::get('version', [ApiController::class, 'version']);

// Authenticate User
Route::prefix('auth')->name('auth.')->group(function () {
	//Route::post('token', [ApiController::class, 'getTokenLogin']);
	Route::post('tokenApp', [ApiController::class, 'getTokenApp']);
    //Route::post('register', [RegisteredUserController::class, 'store']);
	Route::post('registerApp', [ApiController::class, 'getRegister']);
	Route::middleware('auth:sanctum')->group(function () {
		Route::post('update', [UserController::class, 'updateProfile']);
		//Route::get('user', [ApiController::class, 'loginUser']);
		Route::get('logout', [ApiController::class, 'logoutUser']);
		Route::prefix('favorite')->group(function () {
			Route::post('add', [ApiController::class, 'addFavoriteAnime']);
			Route::post('delete', [ApiController::class, 'deleteFavoriteAnime']);
			Route::post('list', [ApiController::class, 'listFavoriteAnime']);
		});
		Route::prefix('view')->group(function () {
			Route::post('add', [ApiController::class, 'addViewAnime']);
			Route::post('delete', [ApiController::class, 'deleteViewAnime']);
			Route::post('list', [ApiController::class, 'listViewAnime']);
		});	
		Route::prefix('watching')->group(function () {
			Route::post('add', [ApiController::class, 'addWatchingAnime']);
			Route::post('delete', [ApiController::class, 'deleteWatchingAnime']);
			Route::post('list', [ApiController::class, 'listWatchingAnime']);
		});			
	});
});