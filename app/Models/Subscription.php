<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Overtrue\LaravelFavorite\Traits\Favoriteable;
use Overtrue\LaravelFollow\Followable;
use Overtrue\LaravelSubscribe\Traits\Subscribable;

class Subscription extends Model
{
    
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
     * Anime Page
     */	
	public function getMySubscriptionAnimes($request)
    {
        return $this->where('user_id',$request->user_id)
            ->join('animes', 'animes.id', '=', 'subscriptions.subscribable_id')
            ->select('animes.id','animes.slug','animes.poster')
            ->get();
    }	
	
}
