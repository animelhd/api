<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
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
    ];
	
    //EndPoint App
    public function getServersList()
    {
        return $this->select('id', 'title as name', 'position')	
			->where('status', 0)
			->get();
    }	
}
