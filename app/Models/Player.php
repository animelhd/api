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
			->where('episode_id', '<=', 20809)	
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
            ->orwhere('updated_at', '>=', '2023-04-02 00:00:04')
			->where('episode_id', '<=', 20809)
			->where('languaje', '=', 1)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	public function getPlayersNew()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '>=', 20231)
			->where('episode_id', '<=', 20809)	
			->where(function ($query) {
				$query->where('server_id', 1)
					  ->orWhere('server_id', 5);
			})
			->orwhere('updated_at', '>=', '2023-07-05 00:00:05')
			->where('episode_id', '<=', 20809)
			->where('languaje', '=', 1)
			->where(function ($query) {
				$query->where('server_id', 1)
					  ->orWhere('server_id', 5);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	public function getPlayersList()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('updated_at', '>=', '2023-08-11 17:33:29')
			->where('episode_id', '<=', 20231)	
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
			->where('episode_id', '<=', 20809)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->orderby('players.id','desc')
			->limit(1)
			->first();
    }
	public function getPlayerList()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('updated_at', '>=', '2023-08-11 17:33:35')
			->where('episode_id', '<=', 20671)	
			->where(function ($query) {
				$query->where('server_id', 1);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
	//New App 1.0.3 y Tienda
	public function getRecentPlayers()
    {
        return $this->cacheFor(now()->addHours(12))
			->select('players.id', 'code as link', 'languaje as language', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id', '>=', 20804)
			->where('episode_id', '<=', 20809)	
			->where(function ($query) {
				$query->where('server_id', 1)
					->orWhere('server_id', 5);
			})
			->orwhere('created_at', '>=', '2023-08-31 18:20:01')
			->where('episode_id', '<=', 20809)
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
        return $this
			->select('players.id', 'code as link', 'languaje as language', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('updated_at', '>=', '2023-08-31 17:33:35')
			->where('episode_id', '<=', 20809)	
			->where(function ($query) {
				$query->where('server_id', 1)
					  ->orWhere('server_id', 5);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }

	//App Nueva
	public function getPlayerApp($request)
	{
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'title as name', 'code as link', 'embed', 'episode_id', 'number', 'anime_id as animeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->leftJoin('episodes', 'episodes.id', '=', 'players.episode_id')
			->where('players.id', $request->player_id)
			->first();
    }

}