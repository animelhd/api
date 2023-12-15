<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Rennokki\QueryCache\Traits\QueryCacheable;

class Player extends Model
{

	use QueryCacheable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
	public $timestamps = true;
	public $updated_at = true;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'server_id' => 'integer',
        'episode_id' => 'integer',
    ];


    public function server()
    {
        return $this->belongsTo(\App\Models\Server::class);
    }

    public function episode()
    {
        return $this->belongsTo(\App\Models\Episode::class);
    }


	public function app2_getPlayersEpisode($request)
    {

		$results = $this
			->select('code', 'server_id', 'languaje', 'title', 'embed', 'servers.status')
			->where('animes.slug', $request->anime_slug)
			->where('episodes.number', $request->episode_number)
			->join('episodes', 'episodes.id', 'players.episode_id')
			->join('animes', 'animes.id', 'episodes.anime_id')
			->join('servers', 'servers.id', 'players.server_id')
			->get();

		$groupedData = $results->groupBy('languaje')->map(function ($group) {
			return $group->values();
		});

		$groupedData = $groupedData->all(); // Convertir el resultado en un array simple

		$response = [];

		// Establecer los índices 0 y 1
		$response[0] = $groupedData[0] ?? [];
		$response[1] = $groupedData[1] ?? [];

		return $response;
    }

	public function web_getEpisodesReleases()
    {
		return $this->cacheFor(now()->addHours(24))
			->select('animes.name', 'animes.slug','animes.banner', 'animes.poster', 'players.created_at', 'episodes.number', 'players.id', 'players.languaje')
			->leftJoin('episodes', 'episodes.id', 'players.episode_id')
			->leftJoin('animes', 'animes.id', 'episodes.anime_id')
			->join(\DB::raw("(SELECT animes.id as anime_id, MAX(episodes.id) as episode_id
						FROM episodes
						JOIN animes ON animes.id = episodes.anime_id
						GROUP BY animes.id) latest_episodes"), function ($join) {
							$join->on('latest_episodes.anime_id', '=', 'animes.id')
							->on('latest_episodes.episode_id', '=', 'episodes.id');
						})
			->leftJoin(\DB::raw("(SELECT episode_id, MAX(id) as max_player_id
						FROM players
						GROUP BY episode_id) max_players"), function ($join) {
							$join->on('max_players.episode_id', '=', 'episodes.id');
						})
			->whereColumn('max_players.max_player_id', '=', 'players.id')
			->where('episodes.id', '<=', 21844)
			->where('animes.status', 1)
			->limit(24)
			->distinct('episodes.id')
			->orderBy('players.id', 'desc')
			->get();
	}
	
	public function getPlayersEpisode($request, $episode)
    {
        return $this
			->select('players.id','code','languaje','server_id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id',$episode->id)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->with(['server'])
			->get()
			->groupby('languaje');
    }

    public function getPlayersEpisodeNew($request, $episode)
    {
        return $this
			->select('players.id','languaje','server_id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id',$episode->id)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 2);
			})
			->with(['server'])
			->get()
			->groupby('languaje');
    }
    
    //Endpoint App
	public function getPlayersRecent()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '>=', 19529)
			->where('episode_id', '<=', 21844)	
			->where(function ($query) {
				$query->where('server_id', 1);
			})
            ->orwhere('updated_at', '>=', '2023-04-02 00:00:08')
			->where('episode_id', '<=', 21844)
			->where('languaje', '=', 1)
			->where(function ($query) {
				$query->where('server_id', 1);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	public function getLastPlayer2()
	{
        return $this->cacheFor(now()->addHours(1))
			->select('players.id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '<=', 21844)
			->where(function ($query) {
				$query->where('server_id', 1);
			})
			->orderby('players.id','desc')
			->limit(1)
			->first();
    }
	//New App 1.0.3 y Tienda
	public function getRecentPlayers()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje as language', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '>=', 20809)
			->where('episode_id', '<=', 21844)
			->where(function ($query) {
				$query->where('server_id', 1)
					->orWhere('server_id', 5);
			})
			->orwhere('created_at', '>=', '2023-09-01 19:35:28')
			->where('episode_id', '<=', 21844)
			->where('languaje', '=', 1)
			->where(function ($query) {
				$query->where('server_id', 1)
					->orWhere('server_id', 5);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	public function getPlayersList2()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje as language', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			//->where('updated_at', '>=', '2023-09-06 19:35:34')
			->where('episode_id', '>=', 10000)	
			->where('episode_id', '<=', 21844)	
			->where(function ($query) {
				$query->where('server_id', 1)
					  ->orWhere('server_id', 5);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	public function getLastPlayer()
	{
        return $this->cacheFor(now()->addHours(1))
			->select('players.id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '<=', 21844)
			->where(function ($query) {
				$query->where('server_id', 1);
			})
			->orderby('players.id','desc')
			->limit(1)
			->first();
    }
	//Version 1.0.4
	public function getNewPlayers()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje as language', 'server_id', 'episode_id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '>=', 21677)
			->where('episode_id', '<=', 21844)
			->where(function ($query) {
				$query->where('server_id', 1)
					->orWhere('server_id', 5);
			})
			->orwhere('created_at', '>=', '2023-09-01 19:35:28')
			->where('episode_id', '<=', 21844)
			->where('languaje', '=', 1)
			->where(function ($query) {
				$query->where('server_id', 1)
					->orWhere('server_id', 5);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	public function getNewPlayers2($request)
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje as language', 'server_id', 'episode_id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '>=', $request->id_episode)
			->where('episode_id', '<=', 21844)
			->where(function ($query) {
				$query->where('server_id', 1)
					->orWhere('server_id', 5);
			})
			->orwhere('created_at', '>=', '2023-09-01 19:35:28')
			->where('languaje', '=', 1)
			->where(function ($query) {
				$query->where('server_id', 1)
					->orWhere('server_id', 5);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	public function getPlayerApp($request)
	{
        return $this->select('players.id', 'code as link', 'languaje as language', 'server_id', 'episode_id')
			->where('players.id', $request->id)
			->first();
    }
}