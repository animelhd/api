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
	public function getPlayersRecents()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje', 'embed', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
            ->where('updated_at', '>=', '2023-04-01 23:59:13')
			->where('episode_id', '<=', 19651)	
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }

	public function getPlayersList($request)
    {
        return $this->cacheFor(now()->addHours(24))
			->select('players.id', 'code as link', 'languaje', 'embed', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
            ->where('updated_at', '>=', '2023-04-01 23:59:13')
			->where('episode_id', '<=', 19651)	
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
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
			->where('episode_id', '<=', 19651)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->orderby('players.id','desc')
			->limit(1)
			->first();
    }
	//App Nueva
	public function getPlayerApp($request)
	{
        return $this->select('players.id', 'title as name', 'code as link', 'embed', 'episode_id', 'number', 'anime_id as animeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->leftJoin('episodes', 'episodes.id', '=', 'players.episode_id')
			->where('players.id', $request->player_id)
			->first();
    }

}