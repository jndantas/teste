<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Database;

class User{

    /**
     * Get users view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $users = Database::table("users")->where("company", 0)->get();
        $usersData = array();
        foreach ($users as $user) {
            $usersData[] = array(
                                                "user" => $user,
                                                "files" => Database::table("files")->where("uploaded_by" , $user->id)->count("id", "files")[0]->files,
                                                "disk" => Database::table("files")->where("uploaded_by" , $user->id)->sum("size", "size")[0]->size
                                            );
        }
        $user = Auth::user();
        if ($user->role != "superadmin") {
            return view('errors/404');   
        }
        $users = $usersData;
        return view('users', compact("user", "users"));
    }

    /**
     * Create user account
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
        $signup = Auth::signup(
            array(
                "fname" => escape(input('fname')),
                "lname" => escape(input('lname')),
                "phone" => escape(input('phone')),
                "email" => escape(input('email')),
                "address" => escape(input('address')),
                "avatar" => $avatar,
                "role" => "user",
                "company" => 0,
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
            exit(json_encode(responder("success", "Account Created", "Account successfully created","reload()")));
        }else{
            if (!empty(($avatar))) {
                File::delete($avatar, "avatar");
            }
            exit(json_encode(responder("error", "Oops!", $signup["message"])));
        }
    }

    /**
     * Delete user account
     * 
     * @return Json
     */
    public function delete() {
        $account = Database::table("users")->where("id", input("userid"))->first();
        if (!empty($account->avatar)) {
            File::delete($account->avatar, "avatar");
        }
        Database::table("users")->where("id", input("userid"))->delete();
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Account Deleted!", "Account successfully deleted.","reload()")));
    }

    /**
     * User update view
     * 
     * @return Json
     */
    public function updateview() {
        $data = array(
                "user" => Database::table("users")->where("id", input("userid"))->first()
            );
        return view('extras/updateuser', $data);
    }

    /**
     * Update user account
     * 
     * @return Json
     */
    public function update() {
        $account = Database::table("users")->where("id", input("userid"))->first();
        foreach (input()->post as $field) {
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
                        Database::table(config('auth.table'))->where("id" , input("userid"))->update(array("avatar" => $avatar['info']['name']));
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token" || $field->index == "userid") {
                continue;
            }
            Database::table(config('auth.table'))->where("id" , input("userid"))->update(array($field->index => escape($field->value)));
        }
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright", "Account successfully updated","reload()")));
    }

}
