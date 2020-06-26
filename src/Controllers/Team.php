<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Database;

class Team{

    /**
     * Get team view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role != "superadmin" && $user->role != "admin") {
            return view('errors/404');   
        }
	   $data = array(
			"user" => Auth::user(),
			"team" => Database::table("users")->where("company", Auth::user()->company)->where("role", "staff")->get()
		);
        return view('team', $data);
    }

    /**
     * Create team member account
     * 
     * @return Json
     */
    public function create() {
        header('Content-type: application/json');
        $password = rand(111111, 999999);
        if (!empty(input('avatar'))) {
            $upload = File::upload(
                input('avatar'), 
                "avatar",
                array(
                    "source" => "base64",
                    "extension" => "png"
                )
            );
            $avatar = $upload['info']['name'];
        }else{
            $avatar = '';
        }
    	if (!isset($_POST['permissions'])) {
    		$_POST['permissions'] = array("upload");
    	}else{
    		array_push($_POST['permissions'], "upload");
    	}
        $signup = Auth::signup(
            array(
                "fname" => escape(input('fname')),
                "lname" => escape(input('lname')),
                "phone" => escape(input('phone')),
                "email" => escape(input('email')),
                "permissions" => json_encode($_POST['permissions']),
                "avatar" => $avatar,
                "role" => "staff",
                "company" => Auth::user()->company,
                "password" => Auth::password($password)
            ), 
            array(
                "uniqueEmail" => input('email')
            )
        );
        if ($signup["status"] == "success") {
            Mail::send(
                input('email'),
                "Welcome to ".env("APP_NAME")."!",
                array(
                    "title" => "Welcome to ".env("APP_NAME")."!",
                    "subtitle" => "A new account has been created for you at ".env("APP_NAME").".",
                    "buttonText" => "Login Now",
                    "buttonLink" => env("APP_URL"),
                    "message" => "These are your login Credentials:<br><br><strong>Email:</strong>".input('email')."<br><strong>Password:</strong>".$password."<br><br>Cheers!<br>".env("APP_NAME")." Team."
                ),
                "withbutton"
            );
            exit(json_encode(responder("success", "Member Added", "Team account successfully created","reload()")));
        }else{
            if (!empty(($avatar))) {
                File::delete($avatar, "avatar");
            }
            exit(json_encode(responder("error", "Oops!", $signup["message"])));
        }
    }

    /**
     * Delete team member account
     * 
     * @return Json
     */
    public function delete() {
        $account = Database::table("users")->where("id", input("memberid"))->first();
        if (!empty($account->avatar)) {
            File::delete($account->avatar, "avatar");
        }
        Database::table("users")->where("id", input("memberid"))->delete();
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Member Deleted!", "Team member successfully deleted.","reload()")));
    }

    /**
     * Team member update view
     * 
     * @return Json
     */
    public function updateview() {
        $data = array(
                "member" => Database::table("users")->where("id", input("memberid"))->first()
            );
        return view('extras/updatemember', $data);
    }

    /**
     * Update team member account
     * 
     * @return Json
     */
    public function update() {
    	if (!isset($_POST['permissions'])) {
    		$_POST['permissions'] = array("upload");
    	}else{
    		array_push($_POST['permissions'], "upload");
    	}
    	Database::table(config('auth.table'))->where("id" , input("memberid"))->update(array("permissions" => json_encode($_POST['permissions'])));
        $account = Database::table("users")->where("id", input("memberid"))->first();
        foreach (input()->post as $field) {
        	if (!isset($field->index)) {
        		continue;
        	}
            if ($field->index == "avatar") {
                if (!empty($field->value)) {
                    $avatar = File::upload(
                        $field->value, 
                        "avatar",
                        array(
                            "source" => "base64",
                            "extension" => "png"
                        )
                    );

                    if ($avatar['status'] == "success") {
                        if (!empty($account->avatar)) {
                            File::delete($account->avatar, "avatar");
                        }
                        Database::table(config('auth.table'))->where("id" , input("memberid"))->update(array("avatar" => $avatar['info']['name']));
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token" || $field->index == "memberid") {
                continue;
            }
            Database::table(config('auth.table'))->where("id" , input("memberid"))->update(array($field->index => escape($field->value)));
        }
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright", "Member account successfully updated","reload()")));
    }
}
