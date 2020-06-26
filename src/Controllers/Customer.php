<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Database;

class Customer{

    /**
     * Get customers view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $customers = Database::table("users")->where("company", Auth::user()->company)->where("role", "user")->get();
        $customersData = array();
        foreach ($customers as $customer) {
            $customersData[] = array(
                                                "user" => $customer,
                                                "files" => Database::table("files")->where("uploaded_by" , $customer->id)->count("id", "files")[0]->files,
                                                "disk" => Database::table("files")->where("uploaded_by" , $customer->id)->sum("size", "size")[0]->size
                                            );
        }
        $user = Auth::user();
        if ($user->role == "user") {
            return view('errors/404');   
        }
        $customers = $customersData;
        return view('customers', compact("user", "customers"));
    }

    /**
     * Create customer account
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
                "email" => escape(input('email')),
                "phone" => escape(input('phone')),
                "address" => escape(input('address')),
                "avatar" => $avatar,
                "role" => "user",
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
            exit(json_encode(responder("success", "Customer Added", "Customer successfully added","reload()")));
        }else{
            if (!empty(($avatar))) {
                File::delete($avatar, "avatar");
            }
            exit(json_encode(responder("error", "Oops!", $signup["message"])));
        }
    }

    /**
     * Delete Customer account
     * 
     * @return Json
     */
    public function delete() {
        $account = Database::table("users")->where("id", input("customerid"))->first();
        if (!empty($account->avatar)) {
            File::delete($account->avatar, "avatar");
        }
        Database::table("users")->where("id", input("customerid"))->delete();
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Customer Deleted!", "Customer successfully deleted.","reload()")));
    }

    /**
     * Customer update view
     * 
     * @return Json
     */
    public function updateview() {
        $data = array(
                "user" => Database::table("users")->where("id", input("customerid"))->first()
            );
        return view('extras/updatecustomer', $data);
    }

    /**
     * Update user account
     * 
     * @return Json
     */
    public function update() {
        $account = Database::table("users")->where("id", input("customerid"))->first();
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
                        Database::table(config('auth.table'))->where("id" , input("customerid"))->update(array("avatar" => $avatar['info']['name']));
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token" || $field->index == "customerid") {
                continue;
            }
            Database::table(config('auth.table'))->where("id" , input("customerid"))->update(array($field->index => escape($field->value)));
        }
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright", "Customer successfully updated","reload()")));
    }

}
