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
	Route::get('home', [ApiController::class, 'getDataHome']);
	Route::get('player/lastplayer', [ApiController::class, 'getLastPlayer2']);
	Route::get('players/{id}', [ApiController::class, 'getPlayerApp']);	
	Route::get('recents', [ApiController::class, 'getRecenteApp']);
	Route::middleware(['throttle:app'])->group(function () {
		//Route::get('newApp', [ApiController::class, 'getNewApp']);
		//Route::get('listApp', [ApiController::class, 'getListApp2']);
	});
	Route::get('listApp3', [ApiController::class, 'getListApp']);
	Route::get('lastPlayer2', [ApiController::class, 'getLastPlayer']);
	Route::get('recentListApp', [AppController::class, 'getRecentApp']);
	Route::middleware('auth:sanctum')->group(function () {
		Route::get('recentListApp2/{id_anime}/{id_episode}', [AppController::class, 'getRecentApp2']);
	});
	Route::middleware('appMd')->group(function () {
		Route::get('recentApp', [ApiController::class, 'getRecentApp']);
		//Route::get('listApp2', [ApiController::class, 'getListApp']);
		Route::get('lastPlayer', [ApiController::class, 'getLastPlayer']);
	});

	Route::get('anime/{anime_slug}', [ApiController::class, 'getDataAnime']);
	Route::prefix('server')->name('server.')->group(function () {
		Route::get('{anime_id}/{episode_number}/{languaje}', [ApiController::class, 'getServerApp']);
	});
	Route::get('view-anime/{id}/{episode_id}', [ApiController::class, 'setViewsAnime']);
	Route::get('view-animes/{id}', [ApiController::class, 'setViewsAnimes']);

});

Route::middleware(['throttle:app'])->group(function () {	
	// Config
	Route::get('config', [ApiController::class, 'config']);
	
});
Route::get('version', [ApiController::class, 'version']);

// Routes appWeb
Route::get('releases', [ApiController::class, 'releases']);
Route::get('releases2', [ApiController::class, 'releases']);
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




// Authenticate User
Route::prefix('auth2')->name('auth2.')->group(function () {
	Route::get('user', [ApiController::class, 'loginUser']);
		Route::prefix('favorite')->group(function () {
			Route::post('list', [ApiController::class, 'listFavoriteAnime']);
		});
		Route::prefix('view')->group(function () {
			Route::post('list', [ApiController::class, 'listViewAnime']);
		});	
		Route::prefix('watching')->group(function () {
			Route::post('list', [ApiController::class, 'listWatchingAnime']);
		});			
});