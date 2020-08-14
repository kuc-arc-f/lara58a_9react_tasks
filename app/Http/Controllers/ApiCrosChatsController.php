<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libs\LibChat;
use App\Libs\AppConst;
use App\Chat;
use App\ChatMember;
use App\ChatPost;
use Carbon\Carbon;

//
class ApiCrosChatsController extends Controller
{
    /**************************************
     *
     **************************************/
    public function __construct(){
        $this->TBL_LIMIT = 200;
    }
    /**************************************
     *
     **************************************/
    public function get_chats(Request $request){
        $data = $request->all();
        $chats = Chat::orderBy('id', 'desc')->skip(0)->take($this->TBL_LIMIT)
        ->get();
        $user_id = $data["user_id"];
        $chat_members = $this->get_chat_members($user_id);
        $chat_items = [];
        foreach($chats as $chat ){
            $valid = $this->valid_member($chat->id , $chat_members);
            $chat["valid_join"] = $valid;
            $chat_items[] = $chat;
        }
        $join_chats = $this->get_join_items($user_id);

        $retArr = [
            'chat_items' => $chat_items,
            'join_chats' => $join_chats,
        ];        
        return response()->json($retArr );
    }
    /**************************************
     *
     **************************************/
    public function search_chat(Request $request){
        $data = $request->all();
        $user_id = $data["user_id"];
        $search_name = $data["search_name"];

        $chats = Chat::orderBy('id', 'desc')
        ->where("name", "like", "%" . $search_name . "%" )    
        ->skip(0)->take($this->TBL_LIMIT)
        ->get();    
        $chat_members = $this->get_chat_members($user_id);
        $chat_items = [];
        foreach($chats as $chat ){
            $valid = $this->valid_member($chat->id , $chat_members);
            $chat["valid_join"] = $valid;
            $chat_items[] = $chat;
        }
//        $join_chats = $this->get_join_items($user_id);
        $retArr = [
            'chat_items' => $chat_items,
        ];        
        return response()->json($retArr );
    }

    /**************************************
     *
     **************************************/
    public function get_member_info(Request $request){
        $LibChat = new LibChat;

        $data = $request->all();
        $chat_id = $data["chat_id"];
        $user_id = $data["user_id"];

        $chat = Chat::find( $chat_id );
        $chat_members = ChatMember::where('chat_id', $chat_id )
        ->where('user_id' , '<>', $user_id )
        ->get(); 
        $chat_member = ChatMember::where('chat_id', $chat_id )
            ->where('user_id', $user_id)
            ->first();    
        $chat_posts = $LibChat->get_posts($chat_id ,$this->TBL_LIMIT );
        $join_chats = $this->get_join_items($user_id );

        $retArr = [
            'chat_members' => $chat_members,
            "chat_member" => $chat_member ,
            'chat' => $chat,
            'chat_posts' => $chat_posts,
            'join_chats' => $join_chats,
        ];
        return response()->json($retArr );
    }
    /**************************************
     *
     **************************************/
    public function csv_get(){
        $LibChat = new LibChat;
        $csv= $LibChat->csv_get( $this->TBL_LIMIT );
        return response($csv )
        ->withHeaders([
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="chats.csv"',
        ]);                
        exit();
    }    
    /**************************************
     *
     **************************************/
    private function get_chat_members($user_id){
        $chat_members = ChatMember::select([
            "chats.id",
            "chat_members.user_id"
        ])
        ->join('chats',
            'chat_members.chat_id' ,'=', 'chats.id'
        )
        ->where('chat_members.user_id', $user_id)
        ->get();
        return  $chat_members;
    }    
    /**************************************
     * valid-join-chat
     **************************************/
    private function valid_member($chat_id , $chat_members){
        $ret = false;
        foreach($chat_members  as $chat_member ){
            if($chat_id == $chat_member->id){
                $ret = true;
    //debug_dump($chat_member->id);
            }
        }
        return $ret;
    }
    /**************************************
     *
     **************************************/    
    public function add_member(Request $request){
        $data = $request->all();
        $user_id = $data["user_id"];
        $chat_id = $data["chat_id"];
        $checkMember = $this->get_memberExist($chat_id, $user_id);
        if(!empty($checkMember)){
            $retArr = [
                'return' => 0,
                "error_msg" => "Error, this chat already Joined.",
            ];
            return response()->json($retArr );
        }
        $data = [
            "user_id" => $user_id,
            "chat_id" => $chat_id,
        ];
        $chat_member = new ChatMember();
        $chat_member->fill($data );
        $chat_member->save();        

        $retArr = [
            'return' => 1,
            "error_msg" => "",
        ];
        return response()->json($retArr );
    }
    /**************************************
     *
     **************************************/ 
    public function delete_member(Request $request){
        $data = $request->all();
        $user_id = $data["user_id"];
        $chat_id = $data["chat_id"];
        $data = [
            "user_id" => $user_id,
            "chat_id" => $chat_id,
        ];
        $chat_member = ChatMember::where('chat_id', $chat_id )
        ->where('user_id', $user_id)
        ->first();
        if(!empty($chat_member)){
            $member = ChatMember::find($chat_member->id);
            $member->delete();
        }
        $retArr = [
            'return' => 1,
            "error_msg" => "",
        ];
        return response()->json($retArr );
    }   
    /**************************************
     *
     **************************************/    
    private function get_memberExist($chat_id, $user_id){
        $chat_members = ChatMember::where('chat_id', $chat_id )
        ->where("user_id", $user_id )
        ->get();
        return $chat_members->toArray();
    }    
    /**************************************
     *
     **************************************/  
    private function get_join_items($user_id ){
        $join_chats = Chat::orderBy('chats.id', 'desc')
        ->select([
            'chats.id',
            'chats.name',
            'chats.user_id',
            'chats.created_at',                
        ])
        ->join('chat_members','chat_members.chat_id','=','chats.id')
        ->where('chat_members.user_id', $user_id)
        ->get();
        return $join_chats;
    }
    /**************************************
     *
     **************************************/  
    public function get_join_chats(Request $request){
        $data = $request->all();
        $user_id = $data["user_id"];
        $join_chats = $this->get_join_items($user_id );

        return response()->json($join_chats );
    } 
    /**************************************
     *
     **************************************/  
    public function create_chat(Request $request)
    {
        $date = $request->all();
        $chat = new Chat();
        $chat->fill($date );
        $chat->save();
        
        return response()->json($date );
    }     
    /**************************************
     *
     **************************************/  
    public function update_chat(Request $request){
        $data = $request->all();

        $chat = Chat::find($data["id"] );
        $chat->fill($data);
        $chat->save();

        return response()->json($data );
    }
    /**************************************
     *
     **************************************/  
    public function delete_chat(Request $request){
        $data = $request->all();
        $chat = Chat::find($data["id"] );
        $chat->delete();

        return response()->json($data );
    }
    /**************************************
     *
     **************************************/  
    public function delete_post(Request $request){
        $data = $request->all();
		$id = (int)$data["id"];
		$chat_post = ChatPost::find($id);
		$chat_post->delete();        
        return response()->json($data );
    }
    /**************************************
     *
     **************************************/
    public function info_chat(Request $request){
        $data = $request->all();
        $id = $data["chat_id"];
        $chat = Chat::find($id);
        $members = ChatMember::select([
            'chat_members.id',
            'chat_members.user_id',
            'chat_members.created_at',
            'chat_members.token',            
            'users.name as user_name',
        ])
        ->join('users','users.id','=','chat_members.user_id')
        ->where('chat_members.chat_id', $id)
        ->orderBy('chat_members.id', 'desc')
        ->skip(0)->take($this->TBL_LIMIT)
        ->get();
        $retArr = [
            'members' => $members,
            'chat' => $chat,
        ];        
        return response()->json($retArr );
    }

}
