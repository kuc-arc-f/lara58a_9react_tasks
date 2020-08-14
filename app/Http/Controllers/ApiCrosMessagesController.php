<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Message;
use App\User;
//
class ApiCrosMessagesController extends Controller
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
    public function create_message(Request $request){
		$data = $request->all();
        $user_id = $data["user_id"];

		$message = new Message();
		$message["user_id"]= $user_id;
		$message["from_id"]= $user_id;
		$message["to_id"]= $data["to_id"];
		$message["type"]= 1;
		$message["status"]= 1;
		$message->fill($data );
        $message->save();
        return response()->json($data);
    }
    /**************************************
     *
     **************************************/
    public function get_message(Request $request)
    {
        $message = Message::find(request('id'));
		//status_set
		$message["status"] = 2;
		$message->save();

		$item = Message::select([
			'messages.id',
			'messages.title',
			'messages.content',
			'messages.from_id',
			'messages.created_at',
			'messages.status',
			'users.name as user_name',
			'users.email as user_email',
		])
        ->leftJoin('users','users.id','=','messages.from_id')
        ->where('messages.id' , request('id') )
		->first();        
//        $ret = ['id'=> request('id') ];
        return response()->json($item );
    }      
    /**************************************
     *
     **************************************/
    public function get_sent_message(Request $request)
    {
        $item = Message::find(request('id'));
        $ret = ['id'=> request('id') ];
        return response()->json($item );
    }  
    /**************************************
     *
     **************************************/
    public function delete_message(Request $request )
    {
        $message = Message::find(request('id') );
        $message->delete();
        $ret = ['id'=> request('id') ];
        return response()->json($ret );
    }   
	/**************************************
     *
     **************************************/
    public function export(){
        if (isset($_GET['id'])){
            $id  = $_GET['id'];
            $message = Message::find($id);
            $dt = new Carbon($message->created_at);
            $datetime = $dt->format('Ymd_Hi');

            $to_user = User::where('id', $message->to_id)
            ->first();
            $from_user = User::where('id', $message->from_id )
            ->first();			
            //text-get
            $stream = fopen('php://temp', 'r+b');
            fwrite($stream, "Title : " . $message->title . "\r\n" );
            fwrite($stream, "Created : " . $message->created_at . "\r\n" );
            fwrite($stream, "From : " . $from_user->name . "\r\n" );
            fwrite($stream, "To : " . $to_user->name . "\r\n" );
            fwrite($stream, "ID : " . $message->id . "\r\n" );
            fwrite($stream, "=========================\r\n" );
            fwrite($stream, $message->content . "\r\n" );
            rewind($stream);
            $csv = stream_get_contents($stream);
            $attachment = "attachment; filename=message_{$datetime}.txt";
//var_dump( $s );
//exit();
            return response($csv )
                ->withHeaders([
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => $attachment,
                ]);			
            exit();
        }
    }         

}
