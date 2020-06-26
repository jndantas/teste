<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Chat{

    /**
     * save chat to database
     * 
     * @return Json
     */
    public function post() {
        header('Content-type: application/json');
    	$user = Auth::user();
    	$data = array(
    					"file" => input("document_key"),
    					"message" => escape($_POST['message']),
    					"sender" => $user->id
    				);
    	Database::table("chat")->insert($data);
    	$chatId = Database::table("chat")->insertId();
    	exit(json_encode($response = array(
                "status" => "success",
                "callback" => "chatResponse('".date("M d, Y h:ia")."', '".input("chatId")."', '".$chatId."')",
                "notify" => false,
                "callbackTime" => "instant"
            )));
    }

    /**
     * fetch chats
     * 
     * @return HTML
     */
    public function fetch() {
        $user = Auth::user();
        $chats = Database::table("chat")->where("file", input("document_key"))->where("chat`.`id", ">" , input("lastChat"))->leftJoin("users", "users.id","chat.sender")->get("chat.id", "chat.message", "chat.time_", "chat.sender", "users.avatar", "users.fname", "users.lname");
        return view('extras/chats', compact("chats", "user"));
    }

}