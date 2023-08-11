<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnimeController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Stream\StreamController;
use App\Http\Controllers\Import\Flv;
use App\Http\Controllers\Import\Tio;
use App\Http\Controllers\Import\Jk;
use App\Http\Controllers\Import\Monos;
use App\Http\Controllers\Import\Fenix;
use App\Http\Controllers\Auth\AdminAuthenticateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('clear', function(){
    // \Artisan::call('cache:clear');
    // \Artisan::call('route:clear');
    // \Artisan::call('view:clear');
    // \Artisan::call('config:clear');
    // \Artisan::call('optimize');
    \Artisan::call('optimize:clear');
    return 'Optimized All Cache';
})->name('clear');

Route::prefix('generate')->name('generate.')->group(function (){
    Route::get('degoo/{id}', [StreamController::class, 'degooLinks'])->name('degoo');
    Route::get('gphotos/{bucket}', [StreamController::class, 'gphotosLinks'])->name('gphotos');
    Route::get('cloud/{lang}/{bucket}', [StreamController::class, 'betaLinks'])->name('cloudLinks');
    Route::get('wasabi/{lang}/{bucket}', [StreamController::class, 'wasabiLinks'])->name('wasabiLinks');
    Route::get('alpha', [StreamController::class, 'alphaLink'])->name('alphaLink');
    Route::get('fire/{id?}', [StreamController::class, 'fireLinks'])->name('fireLinks');
});

Route::prefix('import')->name('import.')->group(function (){
    Route::prefix('flv')->name('flv.')->group(function (){
        Route::prefix('anime')->name('anime.')->group(function (){
            Route::get('/', [Flv::class, 'getAnime'])->name('slug');
            Route::get('episodes', [Flv::class, 'importEpisodes'])->name('episodes');
        });
    });
    Route::prefix('tio')->name('tio.')->group(function (){
        Route::prefix('anime')->name('anime.')->group(function (){
            Route::get('/', [Tio::class, 'getAnime'])->name('slug');
            Route::get('episodes', [Tio::class, 'importEpisodes'])->name('episodes');
            Route::get('perpages', [Tio::class, 'importarEpisodesPerPage'])->name('perpages');
        });
    });
    Route::prefix('jk')->name('jk.')->group(function (){
        Route::prefix('anime')->name('anime.')->group(function (){
            Route::get('/', [Jk::class, 'getAnime'])->name('slug');
            Route::get('episodes', [Jk::class, 'importEpisodes'])->name('episodes');
            Route::get('perpages', [Jk::class, 'importarEpisodesPerPage'])->name('perpages');
        });
    });
    Route::prefix('monos')->name('monos.')->group(function (){
        Route::prefix('anime')->name('anime.')->group(function (){
            Route::get('/', [Monos::class, 'getAnime'])->name('slug');
            Route::get('episodes', [Monos::class, 'importEpisodes'])->name('episodes');
            Route::get('perpages', [Monos::class, 'importarEpisodesPerPage'])->name('perpages');
        });
    });
    Route::prefix('fenix')->name('fenix.')->group(function (){
        Route::prefix('anime')->name('anime.')->group(function (){
            Route::get('/', [Fenix::class, 'getAnime'])->name('slug');
            Route::get('episodes', [Fenix::class, 'importEpisodes'])->name('episodes');
            Route::get('perpages', [Fenix::class, 'importarEpisodesPerPage'])->name('perpages');
        });
    });
});


Route::get('degoo', [StreamController::class, 'degooStream']);

/*
|--------------------------------------------------------------------------
| Stream Routes
|--------------------------------------------------------------------------
*/

Route::get('stream/{id}', [StreamController::class, 'getStream'])->name('stream');
Route::get('generateVideo/{s}/{code}', [StreamController::class, 'generateVideo'])->name('generateVideo');

Route::get('stream2/{id}', [StreamController::class, 'getStream2'])->name('stream2');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminAuthenticateController::class, 'create'])->middleware('guest')->name('adminLogin');
Route::post('/admin/login', [AdminAuthenticateController::class, 'store'])->middleware('guest');
Route::post('/admin/logout', [AdminAuthenticateController::class, 'destroy'])->middleware('authadmin')->name('logout');

Route::middleware('authadmin')->prefix('admin')->name('admin.')->group(function (){
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('animes', AnimeController::class);
    Route::get('animesLatino', [AnimeController::class, 'indexLatino'])->name('animes.indexLatino');
    Route::resource('genres', GenreController::class);
    Route::resource('servers', ServerController::class);
    Route::get('animes-generate', [AnimeController::class, 'generate'])->name('animes.generate');
    Route::name('animes.')->prefix('animes')->group(function () {
        Route::resource('{anime_id}/episodes', EpisodeController::class);
        Route::get('{anime_id}/episodesLatino', [EpisodeController::class, 'indexLatino'])->name('episodes.indexLatino');
        Route::get('{anime_id}/episodes-generate', [EpisodeController::class, 'generate'])->name('episodes.generate');
        Route::get('{anime_id}/players-generate', [EpisodeController::class, 'generatePlayers'])->name('episodes.generatePlayers');
        Route::post('{anime_id}/episodes-alldelete', [EpisodeController::class, 'allDelete'])->name('episodes.allDelete');
        Route::name('episodes.')->prefix('{anime_id}/episodes')->group(function () {
            Route::resource('{episode_id}/players', PlayerController::class);
            Route::post('generate/storePlayers', [PlayerController::class, 'storePlayers'])->name('players.storePlayers');
            Route::post('delete/players-allDelete', [PlayerController::class, 'allDeletePlayers'])->name('players.allDeletePlayers');
        });
    });
});