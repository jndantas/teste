<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Auth;
use Simcify\Database;

class Signature{

    /**
     * save signature
     * 
     * @return Json
     */
    public function save() {
        header('Content-type: application/json');
    	$user = Auth::user();
        $signature = File::upload(
            input("signature"), 
            "signatures",
            array(
                "source" => "base64",
                "extension" => "png"
            )
        );

        if ($signature['status'] == "success") {
            if (!empty($user->signature)) {
                File::delete($user->signature, "signatures");
            }
            Database::table(config('auth.table'))->where("id" , $user->id)->update(array("signature" => $signature['info']['name']));
            exit(json_encode($response = array(
                    "status" => "success",
                    "callback" => "signatureCallback('".url("")."uploads/signatures/".$signature['info']['name']."')",
                    "notify" => false,
                    "callbackTime" => "instant"
                )));
        }else{
            exit(json_encode(responder("error", "Oops!", "Failed to save signature please try again.")));
        }
    }
    
}