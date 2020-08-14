<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Libs\AppConst;

use App\Todo;
//
class ApiCrosTodosController extends Controller
{
	
	/**************************************
	 *
	 **************************************/
	public function __construct(){
		$this->TBL_LIMIT = 500;
	}
    /**************************************
     *
     **************************************/
    public function get_items(Request $request){   
		$data = $request->all();
		$user_id = request('user_id');
//		$ret = ['user_id'=> request('user_id') ];
		$todos = Todo::orderBy('id', 'desc')
		->where("user_id", $user_id )
		->where("complete", request('complete') )
		->limit($this->TBL_LIMIT)
		->get();
		$todo_items = [];
		foreach($todos as $todo ){
			$dt = new Carbon($todo["created_at"]);
			$todo["date_str"] = $dt->format('m-d H:i');
			$todo_items[] = $todo;
		}

		return response()->json($todo_items );
	}
    /**************************************
     *
     **************************************/  
    public function create_todo(Request $request){
		$inputs = $request->all();
        $inputs["complete"] = 0;
        $todo = new Todo();
        $todo->fill($inputs);
        $todo->save();
        $ret = ['title' => request('title'),'content' => request('content')];
        return response()->json($inputs );
    }	 	
	

}