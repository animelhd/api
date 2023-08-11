<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * model instances.
     */
    protected $user;	

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\User;  $user
     * @return void
     */	
	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	public $url;

    public function updateProfile(Request $request)
	{
		try {
			$name = $request->get('name');
			$image = $request->get('image');
			$userUpdate = User::find($request->user()->id);
			if(!$userUpdate)
				throw new Exception("Usuario no encontrado", 1);
			if($image != $userUpdate->image)	
				$userUpdate->image = $image;
			if($name != $userUpdate->name)	
				$userUpdate->name = $name;
			$userUpdate->save();
			return array(
				'status' => 'OK',
				'code' => 200,
				'data' => $userUpdate
			);
		} catch (Exception $error) {
			return array(
				'status' => 'Error',
				'message' => $error->getMessage(),
				'code' => $error->getCode()
			);
		}
		
	}

}