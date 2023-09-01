<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use Rennokki\QueryCache\Traits\QueryCacheable;

class Episode extends Model
{

    use QueryCacheable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
	public $timestamps = false; 
	public $updated_at = true;


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'anime_id' => 'integer',
    ];

    public function players()
    {
        return $this->hasMany(\App\Models\Player::class);
    }

    public function anime()
    {
        return $this->belongsTo(\App\Models\Anime::class);
    }
	
	public function getReleases()
    {
        return $this
            ->select('id','anime_id','number','created_at','views')
			->limit(24)
			->with(['anime' => function ($q) {
				$q->select('id','name','slug','poster','banner');
			}])
		    ->orderby('created_at','desc')
			->get();
    }
	
	public function getInfoEpisodePage($request, $anime)
    {
        return $this
            ->select('id','number','views')
		    ->where('anime_id',$anime->id)
			->where('number',$request->episode_number)
			->first();
    }		
	
	public function getAnteriorEpisodePage($request, $anime)
    {
        return $this->select('number')
			->where('anime_id',$anime->id)
			->where('number',$request->episode_number-1)
			->first();
    }	

	public function getSiguienteEpisodePage($request, $anime)
    {
        return $this
            ->select('number')
			->where('anime_id',$anime->id)
			->where('number',$request->episode_number+1)
			->first();
    }

    //EndPoints App
    public function getEpisodesRecent()
    {
        return $this->cacheFor(now()->addHours(24))
            ->select('id', 'number', 'anime_id as animeId', 'created_at as createdAt', 'views as visitas')
            ->where('id', '>=', 19529)
            ->where('id', '<=', 20809)
		    ->orderby('episodes.id','desc')
			->get();
    }

    public function getEpisodesNew()
    {
        return $this->cacheFor(now()->addHours(24))
            ->select('id', 'number', 'anime_id as animeId', 'created_at as createdAt', 'views as visitas')
            ->where('id', '>=', 20231)
            ->where('id', '<=', 20809)
            ->orderby('episodes.id','desc')
			->get();
    }

    public function getEpisodesList()
    {
        return $this->cacheFor(now()->addHours(24))
            ->select('id', 'number', 'anime_id as animeId', 'created_at as createdAt')
			->where('id', '>=', 20231)
            ->where('id', '<=', 20231)
            ->orderby('episodes.id','desc')
			->get();
    }

    public function getEpisodeList()
    {
        return $this->cacheFor(now()->addHours(24))
            ->select('id', 'number', 'anime_id as animeId', 'created_at as createdAt', 'views as visitas')
            ->where('id', '>=', 20662)
            ->where('id', '<=', 20662)
		    ->orderby('episodes.id','desc')
			->get();
    }
    //New App 1.0.3 y Tienda
    public function getRecentEpisodes()
    {
        return $this->cacheFor(now()->addHours(12))
            ->select('id', 'number', 'anime_id as animeId', 'created_at as createdAt')
            ->where('id', '>=', 20804)
            ->where('id', '<=', 20809)
		    ->orderby('episodes.id','desc')
			->get();
    }

    public function getEpisodesList2()
    {
        return $this
            ->select('id', 'number', 'anime_id as animeId', 'created_at as createdAt')
            ->where('updated_at', '>=', '2023-08-31 17:33:35')
            ->where('id', '<=', 20809)
		    ->orderby('episodes.id','desc')
			->get();
    }
}
