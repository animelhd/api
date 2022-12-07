<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

use Animelhd\AnimesFavorite\Traits\Favoriteable;
use Animelhd\AnimesView\Traits\Vieweable;
use Animelhd\AnimesWatching\Traits\Watchingable;

class Anime extends Model
{
    use Sluggable;

	use Favoriteable;
	use Vieweable;
	use Watchingable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
	public $timestamps = false; 
	public $updated_at = false;


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'aired',
    ];

    public function episodes()
    {
        return $this->hasMany(\App\Models\Episode::class);
    }

    public function mylist()
    {
        return $this->hasMany(\App\Models\MyList::class);
    }
    
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
	
	public function getBeingWatched()
    {
        return $this->select([
				\DB::raw("sum(episodes.views) as totalviews")
			,'name','slug','poster','aired'])
			->leftJoin('episodes','episodes.anime_id','=','animes.id')
			->groupBy('animes.id')
			->orderBy('totalviews','desc')
			->limit(14)			
			->get();
    }	
	public function getPopularToday()
    {
        return $this
			->select('name','slug','poster','vote_average','aired')
            ->orderBy('vote_average','desc')			
			->limit(28)
			->get();
    }	

	public function getAnimesLatino()
    {
        return $this->select('name', 'slug', 'poster', 'vote_average','status',
		     \DB::raw('MAX(number) as number'),\DB::raw('MAX(players.id) as idplayer'))
			->LeftJoin('episodes', 'episodes.anime_id', '=', 'animes.id')
			->LeftJoin('players','episode_id', '=', 'episodes.id')
			->where('players.languaje', '=', 1)
			->groupBy('animes.id')
			->orderBy('idplayer','desc')
			->get();
    }
	public function getListAnimes($request)
    {
        $data = $this->select('name', 'slug', 'poster', 'aired', 'vote_average')
		->orderBy('aired','desc');
		if($request->type){
			$data = $data->where('type',$request->type);
		}
		if(isset($request->status)){
			$data = $data->where('status',$request->status);
		}
		if($request->year){
			$data = $data->whereYear('aired',$request->year);
		}
		if($request->genre){
			$data = $data->where('genres','LIKE',"%{$request->genre}%");
		}
		if($request->search){
			$data = $data->where('name','LIKE',"%{$request->search}%");
			$data = $data->orwhere('name_alternative','LIKE',"%{$request->search}%");
		}
		$data = $data->simplePaginate(28);
		return $data;
    }
	
	public function getUpcomingEpisodes()
    {
        return $this->where('status',1)
			->select('name','slug','banner','broadcast',
				\DB::raw('(select created_at from episodes where anime_id = animes.id order by number desc limit 1) as date'),
				\DB::raw('(select HOUR(created_at) from episodes where anime_id = animes.id order by number desc limit 1) as hour'),
				\DB::raw('(select number from episodes where anime_id = animes.id order by number desc limit 1) as lastEpisode'))
			->orderBy('hour','asc')
			->get()
			->groupBy('broadcast');
    }

	public function getSearch($request)
    {
        return $this->select('name','slug','type','poster')
			->where('name','LIKE',"%{$request->search}%")
			->orwhere('name_alternative','LIKE',"%{$request->search}%")
			->orwhere('overview','LIKE',"%{$request->search}%")
			->orwhere('genres','LIKE',"%{$request->search}%")
			->orwhere('aired','LIKE',"%{$request->search}%")
	        ->orderBy('aired','desc')
	        ->limit(20)
			->get();
    }	
	
	public function getAnimePage($request)
    {
        return $this->select(['animes.*',
				\DB::raw("IFNULL(sum(episodes.views),0) as totalviews")
			])
			->with(['episodes' => function ($q) {
				$q->orderBy('number', 'desc');
				$q->select('id','anime_id','number','views','created_at');
			}])
			->leftJoin('episodes','episodes.anime_id','=','animes.id')
			->where('slug',$request->anime_slug)			
			->groupBy('animes.id')
			->first();
    }

    /**
     * Anime Page
     */	

	public function getAnimeInfoPage($request)
    {
        return $this->where('slug',$request->anime_slug)			
			->first();
    }

	public function getAnimeEpisodePage($request)
    {
        return $this->select('id','name','slug','banner','poster')
			->where('slug',$request->anime_slug)			
			->first();
    }

	public function getAnimeId($request)
    {
        return $this->select('animes.id', 'animes.views', \DB::raw("sum(episodes.views) as totalviews"))
			->leftJoin('episodes','episodes.anime_id','=','animes.id')
			->where('animes.id',$request->id)	
			->groupBy('animes.id')		
			->first();
    }

	/**
     * Filtros
     */
	public function getRecommendations($anime)
    {
		$first_name = explode(' ',trim($anime->name));
		$first_name = $first_name[0];

		$genres = explode(',',trim($anime->genres));
		$first_genre = '';
		$second_genre = '';

		if(count($genres) >= 2){
			$randoms = array_rand($genres, 2);
			$first_genre = $genres[$randoms[0]];
			$second_genre = $genres[$randoms[1]];
		}

        return $this->select('name','slug','banner')
			->where('genres','LIKE',"%{$first_genre}%")
			->where('genres','LIKE',"%{$second_genre}%")
			->where('slug','!=',$anime->slug)
			->limit(10)
			->inRandomOrder()
			->get();
    }
	
    /**
     * Filtros
     */
	public function getFilterYears()
    {
        return $this->select(\DB::raw('YEAR(aired)as year'))
			->distinct()
			->orderBy('aired','desc')
			->get();
    }
	public function getFilterTypeAnime()
    {
        return $this->select(\DB::raw('type'))
			->distinct()
			->get();
    }	
	public function getFilterStatusAnime()
    {
        return $this->select(\DB::raw('status'))
			->distinct()
			->get();
    }
	
	//EndPoints App
	public function getAnimesList($request)
    {
        return $this->select('id', 'name', 'name_alternative as nameAlternative', 'slug', 'banner as imagenCapitulo', 'poster as imagen', 'overview', \DB::raw("Date(aired) as aired"), 'type', 'status', 'genres', 'rating', 'trailer', 'vote_average as voteAverage', 'views as visitas', 'isTopic')
			->orderBy('aired','desc')
			->get();
	}
}
