<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\BbsPost;
use App\BbsAnswer;
use App\User;

//
class BbsPostsController extends Controller
{
	/**************************************
	 *
	 **************************************/
	public function __construct()
	{
		$this->middleware('auth');
		$this->TBL_LIMIT = 20;
		$this->SEARCH_TBL_LIMIT = 100;
	}    
	/**************************************
	 *
	 **************************************/
	public function index(Request $request)
	{
		$user = Auth::user();
		$user_id = Auth::id();
		$mode_all = 1;
		$mode_user = 2;

		$display_mode = $mode_all;
        $inputs = $request->all();
        if(isset($inputs["mode"]) ){
            $display_mode = $inputs["mode"];
		}
		if($display_mode == $mode_all){
			/*
			$bbs_posts = BbsPost::orderBy('id', 'desc')
			->where('display', 1 )
			->paginate( $this->TBL_LIMIT );
			*/
			$bbs_posts = BbsPost::orderBy('bbs_posts.id', 'desc')
			->select([
                'bbs_posts.id',
                'bbs_posts.user_id',
                'bbs_posts.title',
				'bbs_posts.display',
				'bbs_posts.created_at',
                'users.name as user_name',
            ])
			->join('users','users.id','=','bbs_posts.user_id')
			->where('bbs_posts.display', 1 )
			->paginate( $this->TBL_LIMIT );
//debug_dump( $bbs_posts );
//exit();
		}else{
			$bbs_posts = BbsPost::orderBy('id', 'desc')
			->select([
                'bbs_posts.id',
                'bbs_posts.user_id',
                'bbs_posts.title',
				'bbs_posts.display',
				'bbs_posts.created_at',
                'users.name as user_name',
			])			
			->join('users','users.id','=','bbs_posts.user_id')
			->where('bbs_posts.user_id', $user_id)
			->paginate( $this->SEARCH_TBL_LIMIT );
		}

		return view('bbs_posts/index')->with(compact(
			'bbs_posts', 'display_mode', 'user'
		 ) );		
	}
    /**************************************
     *
     **************************************/
    public function search_index(Request $request){
		$user = Auth::user();
		$mode_all = 1;
		$mode_user = 2;
		$display_mode = $mode_all;

        $data = $request->all();  
		$params = $data;   
		/*
		$bbs_posts = BbsPost::orderBy('id', 'desc')
		->where('display', 1 )
        ->where("title", "like", "%" . $data["title"] . "%" )
		->paginate($this->SEARCH_TBL_LIMIT);
		*/
		$bbs_posts = BbsPost::orderBy('bbs_posts.id', 'desc')
		->select([
			'bbs_posts.id',
			'bbs_posts.user_id',
			'bbs_posts.title',
			'bbs_posts.display',
			'bbs_posts.created_at',
			'users.name as user_name',
		])		
		->join('users','users.id','=','bbs_posts.user_id')
		->where('bbs_posts.display', 1 )
        ->where("bbs_posts.title", "like", "%" . $data["title"] . "%" )
		->paginate($this->SEARCH_TBL_LIMIT);
		
//debug_dump( $data );        
		return view('bbs_posts/index')->with(compact(
			'bbs_posts' ,'params', 'user', 'display_mode'
		) );
    }	
	/**************************************
	 *
	 **************************************/
	public function create()
	{
		/*
        $bbs_categories= BbsCategory::orderBy('id', 'asc')
        ->get();
        $bbs_categories = array_pluck($bbs_categories, 'name', 'id');
		*/
//debug_dump($bbs_categories );
//exit();
		$bbs_post = new BbsPost();
		return view('bbs_posts/create')->with( compact('bbs_post' ) );
	}	
	/**************************************
	 *
	 **************************************/    
	public function store(Request $request)
	{
		$user_id = Auth::id();
		$inputs = $request->all();
		$inputs["user_id"] = $user_id;
		$inputs["display"] = 1;
//debug_dump($inputs );
//exit();
		$bbs_post = new BbsPost();
		$bbs_post->fill($inputs);
		$bbs_post->save();
		session()->flash('flash_message', 'Completed, new post');
		return redirect()->route('bbs.index');
	}  
	/**************************************
	 *
	 **************************************/   
	public function confirm(Request $request){
		$data = $request->all();
//debug_dump($data );
//exit();
		$bbs_post = new BbsPost();
		$bbs_post->fill($data);
		return view('bbs_posts/confirm')->with( compact('bbs_post' ) );
	} 
    /**************************************
     *
     **************************************/
    public function show($id)
    {
		$user_id = Auth::id();
		$bbs_post = BbsPost::find($id);
		$user = User::find($bbs_post->user_id );
//debug_dump($user );
//exit();
		$bbs_answer = new BbsAnswer();
		$bbs_answers = BbsAnswer::select([
			'bbs_answers.id',
			'bbs_answers.user_id',
			'bbs_answers.content',
			'bbs_answers.created_at',
			'users.name as user_name',
			])
			->join('users','users.id','=','bbs_answers.user_id')
			->where('bbs_answers.bbs_post_id', $id )
			->orderBy('id', 'desc')
			->skip(0)->take($this->SEARCH_TBL_LIMIT )
			->get();
		return view('bbs_posts/show')->with(
			 compact('bbs_post', 'bbs_answer' ,'bbs_answers',
			 'user_id', 'user')
		);
    }	  	    
	/**************************************
	 *
	 **************************************/
	public function edit($id)
	{
		$bbs_post = BbsPost::find($id);
		return view('bbs_posts/edit')->with('bbs_post', $bbs_post );
	}
	/**************************************
	 *
	 **************************************/
    public function update(Request $request, $id)
    {
        $bbs_post = BbsPost::find($id);
        $bbs_post->fill($request->all());
		$bbs_post->save();
		session()->flash('flash_message', 'Completed, save post');
        return redirect()->route('bbs.index');
    }

}
