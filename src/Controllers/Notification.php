<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Notification{

    /**
     * Get notifications view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
    	$user = Auth::user();
    	$notifications = Database::table("notifications")->where("user", $user->id)->orderBy('id', false)->get();
    	$requests = Database::table("requests")->where(array("status" => "Pending", "receiver" => $user->id), "=")
    						->orWhere("status", "Pending")->where("email", $user->email)->get();
        return view('notifications', compact("user", "notifications", "requests"));
    }

    /**
     * Mark notifications as read
     * 
     * @return Json
     */
    public function read() {
    	$user = Auth::user();
    	Database::table("users")->where("id", $user->id)->update(array("lastnotification" => 'NOW()'));
    	exit(json_encode(responder("success", "", "","", false)));
    }

    /**
     * Delete notification
     * 
     * @return Json
     */
    public function delete() {
    	header('Content-type: application/json');
    	$notificationId = input("notificationid");
    	Database::table("notifications")->where("id", $notificationId)->delete();
    	exit(json_encode(responder("success", "", "","", false)));
    }

    /**
     * Count user notifications
     * 
     * @return Json
     */
    public function count() {
    	header('Content-type: application/json');
    	$user = Auth::user();
    	$notifications = Database::table("notifications")->where("user", $user->id)->where("time_",">" , $user->lastnotification)->count("id", "total")[0]->total;
    	$requests = Database::table("requests")->where(array("status" => "Pending", "receiver" => $user->id), "=")
    						->orWhere("status", "Pending")->where("email", $user->email)->count("id", "total")[0]->total;
    	$count = $notifications + $requests;
    	exit(json_encode(responder("success", "", "","updateNotificationsCount(".$count.")", false)));
    }

}
