<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\BbsPost;
use App\BbsAnswer;
use App\User;
use App\Message;

//
class BbsAnswersController extends Controller
{
	/**************************************
	 *
	 **************************************/    
	public function __construct()
	{
		$this->middleware('auth');
		$this->TBL_LIMIT = 200;
	}
	/**************************************
	 *
	 **************************************/    
	public function store(Request $request)
	{
		$user_id = Auth::id();
		$inputs = $request->all();
		$inputs["user_id"] = $user_id;
		$inputs["status"] = 1;
//debug_dump($inputs );
//exit();
		$bbs_answer = new BbsAnswer();
		$bbs_answer->fill($inputs);
		$bbs_answer->save();
		// Message
		$this->send_message($inputs );
		session()->flash('flash_message', 'Completed, Reply save');

		return redirect()->route('bbs.index');
	}	
	/**************************************
	 *
	 **************************************/    
	private function send_message($data){
		$user_id = Auth::id();
		$bbs_post = BbsPost::find($data["bbs_post_id"] );
		$to_id = $bbs_post->user_id;
		//body
		$body = "BBCに返信された新着通知を、システムから自動送信しています。" . "\r\n". "以下、返信内容となります。" . "\r\n";
		$body = $body . "==================================================" . "\r\n";
		//from_user
		$BBS_ADMIN_MAIL = env('BBS_ADMIN_MAIL', '');
		$from_user = User::where('email', $BBS_ADMIN_MAIL )
		->first();
		$from_user_id = 0;
		if(empty($from_user) == false ){
			$from_user_id = $from_user->id;
//debug_dump($from_user_id);
		}
// exit();
		$message = new Message();
		$message["user_id"]= $from_user_id;
		$message["from_id"]= $from_user_id;
		$message["to_id"]= $to_id;
		$message["type"]= 1;
		$message["status"]= 1;
		$message["title"]=  "BBS Reply : ". $bbs_post->title ;
		$message["content"]= $body . $data["content"];
		$message->save();
	}
	/**************************************
	 *
	 **************************************/    
    public function destroy($id)
    {
        $bbs_answer = BbsAnswer::find($id);
        $bbs_answer->delete();
        session()->flash('flash_message', 'delete complete, answer');
        return redirect()->route('bbs.index');
    }
	 
}
