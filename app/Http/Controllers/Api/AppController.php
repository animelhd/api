<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Anime;
use App\Models\Genre;
use App\Models\Episode;
use App\Models\Player;
use App\Models\Server;
use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    /**
     * model instances.
     */
    protected $episode, $anime, $genre, $player, $server, $user;	

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\Episode;  $episode
	 * @param  \App\Models\Anime;  $anime
	 * @param  \App\Models\Genre;  $genre
     * @return void
     */	
	public function __construct(Episode $episode, Anime $anime, Genre $genre, Player $player, Server $server, User $user)
	{
		$this->episode = $episode;
		$this->anime = $anime;
		$this->genre = $genre;
		$this->player = $player;
		$this->server = $server;
		$this->user = $user;
	}

	public function getRecentApp(Request $request)
	{
		try {
			return array(
			    'animes' => $this->anime->getNewAnimes($request),
			    'episodes' => $this->episode->getNewEpisodes($request),
			    'servers' => $this->server->getServersList2($request),
			    'players' => $this->player->getNewPlayers($request)
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getRecentApp2(Request $request)
	{
		try {
			return array(
			    'animes' => $this->anime->getNewAnimes2($request),
			    'episodes' => $this->episode->getNewEpisodes2($request),
			    'servers' => $this->server->getServersList2($request),
			    'players' => $this->player->getNewPlayers2($request)
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
}