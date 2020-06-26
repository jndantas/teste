<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Auth;
use Simcify\Database;
use DotEnvWriter\DotEnvWriter;

class Settings{

    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
    	$data = array(
    			"user" => Auth::user(),
    			"company" => Database::table("companies")->where("id", Auth::user()->company)->first(),
    			"reminders" => Database::table("reminders")->where("company", Auth::user()->company)->get()
    		);
        return view('settings', $data);
    }

    /**
     * Update profile on settings page
     * 
     * @return Json
     */
    public function updateprofile() {
        header('Content-type: application/json');
    	$account = Database::table(config('auth.table'))->where("email" , input("email"))->first();
    	if (!empty($account) && $account->id != Auth::user()->id) {
			exit(json_encode(responder("error", "Oops", input("email"). " already exists.")));
    	}

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
	    				if (!empty(Auth::user()->avatar)) {
	    					File::delete(Auth::user()->avatar, "avatar");
	    				}
    					Database::table(config('auth.table'))->where("id" , Auth::user()->id)->update(array("avatar" => $avatar['info']['name']));
    				}
    			}
    			continue;
    		}

    		if ($field->index == "csrf-token") {
    			continue;
    		}

    		Database::table(config('auth.table'))->where("id" , Auth::user()->id)->update(array($field->index => escape($field->value)));
    	}
		exit(json_encode(responder("success", "Alright", "Profile successfully updated")));
    }

    /**
     * Update company on settings page
     * 
     * @return Json
     */
    public function updatecompany() {
    	foreach (input()->post as $field) {
    		if ($field->index == "csrf-token") {
    			continue;
    		}

    		Database::table("companies")->where("id" , Auth::user()->company)->update(array($field->index => escape($field->value)));
    	}

        header('Content-type: application/json');
		exit(json_encode(responder("success", "Alright", "Company info successfully updated")));
    }

    /**
     * Update reminders on settings page
     * 
     * @return Json
     */
    public function updatereminders() {
        $user = Auth::user();
    	if (empty(input("reminders"))) {
    		Database::table("companies")->where("id" , $user->company)->update(array("reminders" => "Off"));
    	}else{
    		Database::table("companies")->where("id" , $user->company)->update(array("reminders" => "On"));
    	}
    	Database::table("reminders")->where("company" , $user->company)->delete();
    	foreach( input("subject") as $index => $subject ) {
    		$reminder = array(
    				"company" => $user->company,
    				"days" => input("days")[$index],
    				"subject" => escape(input("subject")[$index]),
    				"message" => escape(input("message")[$index])
    			);
    		Database::table("reminders")->insert($reminder);
    	}
        header('Content-type: application/json');
		exit(json_encode(responder("success", "Alright", "Reminders successfully updated")));
    }

    /**
     * Update password on settings page
     * 
     * @return Json
     */
    public function updatepassword() {
	    header('Content-type: application/json');
    	if(hash_compare(Auth::user()->password, Auth::password(input("current")))){
    		Database::table(config('auth.table'))->where("id" , Auth::user()->id)->update(array("password" => Auth::password(input("password"))));
			exit(json_encode(responder("success", "Alright", "Password successfully updated", "reload()")));
    	}else{
    		exit(json_encode(responder("error", "Oops", "You have entered an incorrect password.")));
    	}
    }

    /**
     * Update system settings
     * 
     * @return Json
     */
    public function updatesystem() {
	    header('Content-type: application/json');
	    $envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
	    $env = new DotEnvWriter($envPath);
	    $env->castBooleans();
	    $enableToggle = array("PKI_STATUS", "CERTIFICATE_DOWNLOAD", "NEW_ACCOUNTS","ALLOW_NON_PDF","USE_CLOUD_CONVERT","SHOW_SAAS");
	    foreach ($enableToggle as $key) {
	    	if (empty(input($key))) {
	    		$env->set($key, 'Disabled');
	    	}
	    }
	    if (empty(input("SMTP_AUTH"))) {
	    	$env->set("SMTP_AUTH", false);
	    }
        $env->set("MAIL_SENDER", input("APP_NAME")." <".input("MAIL_USERNAME").">");
    	foreach (input()->post as $field) {
    		if ($field->index == "APP_LOGO") {
    			if (!empty($field->value)) {
    				$upload = File::upload(
					    $field->value, 
					    "app",
					    array(
					        "source" => "base64",
					        "extension" => "png"
					    )
					);

    				if ($upload['status'] == "success") {
    					File::delete(env("APP_LOGO"), "app");
    					$env->set("APP_LOGO", $upload['info']['name']);
	    				$env->save();
    				}
    			}
    			continue;
    		}
    		if ($field->index == "APP_ICON") {
    			if (!empty($field->value)) {
    				$upload = File::upload(
					    $field->value, 
					    "app",
					    array(
					        "source" => "base64",
					        "extension" => "png"
					    )
					);

    				if ($upload['status'] == "success") {
    					File::delete(env("APP_ICON"), "app");
    					$env->set("APP_ICON", $upload['info']['name']);
    					$env->save();
    				}
    			}
    			continue;
    		}

    		if ($field->index == "csrf-token") {
    			continue;
    		}

    		$env->set($field->index, $field->value);
    		$env->save();
    	}

	    exit(json_encode(responder("success", "Alright", "System settings successfully updated", "reload()")));
    }

}
