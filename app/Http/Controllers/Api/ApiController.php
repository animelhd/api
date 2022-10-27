<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Anime;
use App\Models\Genre;
use App\Models\Episode;
use App\Models\Player;
use App\Models\Server;
use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * model instances.
     */
    protected $episode, $anime, $genre, $player, $server, $user;	

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\Episode;  $episode
	 * @param  \App\Models\Anime;  $anime
	 * @param  \App\Models\Genre;  $genre
     * @return void
     */	
	public function __construct(Episode $episode, Anime $anime, Genre $genre, Player $player, Server $server, User $user)
	{
		$this->episode = $episode;
		$this->anime = $anime;
		$this->genre = $genre;
		$this->player = $player;
		$this->server = $server;
		$this->user = $user;
	}
	
	public $url;

	public function getDataHome(Request $request)
	{
		try {
			return array(
				'episodes' => $this->episode->getReleases(),
				'animeN' => $this->anime->getListAnimes($request),
				'animeP' => $this->anime->getPopularToday(),
				'animeV' => $this->anime->getBeingWatched()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function getDataAnime(Request $request)
	{
		try {
			return $this->anime->getAnimeInfoPage($request);
		} catch (Exception $e) {
			return array(
	            'code' => $e->getMessage()
	        );
		}
	}

	public function getAnimeRecommendations(Request $request)
	{
		try {
			$anime = $this->anime->getAnimeInfoPage($request);
			return $this->anime->getRecommendations($anime);
		} catch (Exception $e) {
			return array(
	            'code' => $e->getMessage()
	        );
		}
	}

	public function releases(Request $request)
	{
		try {
			return $this->episode->getReleases();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function trending(Request $request)
	{
		try {
			return array(
			    'popular_today' => $this->anime->getPopularToday()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function moreView(Request $request)
	{
		try {
			return array(
			    'being_watched' => $this->anime->getBeingWatched(),
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}	
	
	public function filterings(Request $request)
	{
	    try {
			return array(
			    'years' => $this->anime->getFilterYears(),
			    'genres' => $this->genre->getFilterGenres(),
			    'types' => $this->anime->getFilterTypeAnime(),
			    'status' => $this->anime->getFilterStatusAnime()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function latino(Request $request){
	    try {
	        return $this->anime->getAnimesLatino();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function listAnimes(Request $request)
	{
		try {
		    return $this->anime->getListAnimes($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function simulcast(Request $request)
	{
		try {
			return $this->anime->getUpcomingEpisodes();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function search(Request $request)
	{
		try {
			return $this->anime->getSearch($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function getAnime(Request $request)
	{
		try {
			$data = $this->anime->getAnimePage($request);
	       	if(!$data){
				return redirect('404');
	        }
			if($request->header('Authorization') && $request->user_id){
				$user = $this->user::find($request->user_id);
				$data = $user->attachFavoriteStatus($data);
				$data = $user->attachSubscriptionStatus($data);
				unset($data->episodes);
			}
			return $data;
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function getAnimeEpisodes(Request $request)
	{
		try {
			$data = $this->anime->getAnimePage($request);
	       	if(!$data){
				return redirect('404');
	        }
			if($request->header('Authorization') && $request->user_id){
				$user = $this->user::find($request->user_id);
				foreach($data->episodes as $episode){
					$episode->viewed = $user->hasLiked($episode);
				}
			}
			return $data;
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function getEpisode(Request $request)
	{
	    try{
	    	$animed = $this->anime->getAnimeEpisodePage($request);
	    	if(!$animed){
	            return redirect('404');
	        }
	        $episoded = $this->episode->getInfoEpisodePage($request, $animed);
	        if(!$episoded){
	            return redirect('404');
	        }
			DB::unprepared('update episodes set views = views+1 where id = '.$episoded->id.'');
	        $anterior = $this->episode->getAnteriorEpisodePage($request, $animed);
	        $siguiente = $this->episode->getSiguienteEpisodePage($request, $animed);
	        $episoded->anime = $animed;
	        $episoded->anterior = $anterior;
	        $episoded->siguiente = $siguiente;
	        $players = $this->player->getPlayersEpisode($request, $episoded);
	        $episoded->players = $players;
	        return $episoded;
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}

	public function getEpisodePlayers(Request $request)
	{
	    try{
	    	$animed = $this->anime->getAnimeEpisodePage($request);
	    	if(!$animed){
	            return redirect('404');
	        }
	        $episoded = $this->episode->getInfoEpisodePage($request, $animed);
	        if(!$episoded){
	            return redirect('404');
	        }
			DB::unprepared('update episodes set views = views+1 where id = '.$episoded->id.'');
	        $anterior = $this->episode->getAnteriorEpisodePage($request, $animed);
	        $siguiente = $this->episode->getSiguienteEpisodePage($request, $animed);
	        $episoded->anime = $animed;
	        $episoded->anterior = $anterior;
	        $episoded->siguiente = $siguiente;
	        $players = $this->player->getPlayersEpisodeNew($request, $episoded);
	        $episoded->players = $players;
	        return $episoded;
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}
	
	public function sitemap(Request $request){
		try {
			$animes = Anime::orderBy('aired','desc')
			->select('slug', 'status')
			->get();
			$episodios = Episode::orderBy('episodes.created_at', 'desc')
			->select('slug', 'number', 'status')
			->leftJoin('animes','animes.id','=','anime_id')
			->get();			
			return array(
			    'animes' => $animes,
			    'capitulos' => $episodios,
			);	
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}   	

	/**
     * Api Login
     *
     */

	public function getTokenLogin(Request $request){
		try {
			return $this->user->getToken($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}  

	public function loginUser(Request $request){
		try {
			return $this->user->login($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}

	public function logoutUser(Request $request){
		try {
			return $this->user->logout($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}

	public function addFavoriteAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->favorite($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function deleteFavoriteAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unfavorite($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function listFavoriteAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getFavoriteItems(Anime::class)->select('id','name','slug','poster')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}
	
	public function addViewAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->view($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function deleteViewAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unview($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function listViewAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getViewItems(Anime::class)->select('id','name','slug','poster')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}

	public function addWatchingAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->watching($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function deleteWatchingAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unwatching($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function listWatchingAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getWatchingItems(Anime::class)->select('id','name','slug','poster')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}

	//EndPoints App
	//Lista de Animes EndPoint App Nueva
	public function getAnimesList(Request $request)
	{
		try {
			return $this->anime->getAnimesList($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	//Lista de Episodes EndPoint App Nueva
	public function getEpisodesList(Request $request)
	{
		try {
			return $this->episode->getEpisodesList($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	//Lista de Servers EndPoint App Nueva
	public function getServersList(Request $request)
	{
	    try{
 	        return $this->server->getServersList();
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}
	//Lista de Players EndPoint App Nueva	
	public function getPlayersList(Request $request)
	{
	    try{
 	        return $this->player->getPlayersList($request);
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}

	public function config(Request $request)
	{
		if($request->get('v') == '3.1.1'){
			return array(
				'videos' => false,
				'imagenes' => false,
				'updates' => true,
				'latinos' => true,
				'perfil' => false,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}else if($request->get('v') == 302){
			return array(
				'videos' => false,
				'imagenes' => true,
				'updates' => true,
				'latinos' => true,
				'perfil' => true,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}else if($request->get('v') == 303){
			return array(
				'videos' => false,
				'imagenes' => false,
				'updates' => false,
				'latinos' => false,
				'perfil' => false,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}else {
			return array(
				'videos' => true,
				'imagenes' => true,
				'updates' => true,
				'latinos' => true,
				'perfil' => true,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}
	}

}