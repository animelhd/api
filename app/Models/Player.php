<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
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
        return $this->select('players.id','code','languaje','server_id')
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
        return $this->select('players.id','languaje','server_id')
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
	public function getPlayersRecentList()
    {
        return $this->select('players.id', 'code as link', 'languaje', 'embed', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
            ->where('episode_id', '>=', 18331)
			->where('episode_id', '<=', 18820)	
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->orwhere('episode_id', '>=', 13309)
			->where('episode_id', '<=', 13369)	
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
        return $this->select('players.id', 'code as link', 'languaje', 'embed', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('updated_at', '>=', '2023-01-05 13:53:04')
			->where('episode_id', '<=', 18750)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->paginate(1000);
    }

	public function getLastPlayer()
	{
        return $this->select('players.id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->orderby('players.id','desc')
			->limit(1)
			->first();
    }

}