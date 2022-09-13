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
	public $timestamps = false;

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
        return $this->select('id','code','languaje','server_id')
			->where('episode_id',$episode->id)
			->with(['server'])
			->get()
			->groupby('languaje');
    }

    //Endpoint App
	public function getPlayersList($request)
    {
        return $this->select('players.id', 'code as link', 'languaje', 'server_id as serverId', 'episode_id as episodeId')
			->leftJoin('servers','servers.id','=','players.server_id')		
			->where('status', 0)
			->orderby('languaje','desc')
			->limit($request->limit)
			->offset($request->offset)
			->get();
    }	
}
