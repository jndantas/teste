<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Field{

    /**
     * save field to database
     * 
     * @return Json
     */
    public function save() {
        header('Content-type: application/json');
        $user = Auth::user();
        if (isset($_POST['type'])) { 
            $type = "input";
        }else{ 
            $type = "custom";  
        }
        $data = array(
                        "label" => escape($_POST['fieldlabel']),
                        "value" => escape($_POST['fieldvalue']),
                        "type" => $type,
                        "user" => $user->id,
                        "company" => $user->company
                    );
        Database::table("fields")->insert($data);
        $fieldId = Database::table("fields")->insertId();
        if (isset($_POST['type'])) { 
            $callback = "inputFieldResponse('".input("fieldId")."', '".$fieldId."')"; 
        }else{ 
            $callback = "fieldResponse('".input("fieldId")."', '".$fieldId."')"; 
        }
        exit(json_encode($response = array(
                "status" => "success",
                "callback" => $callback,
                "notify" => false,
                "callbackTime" => "instant"
            )));
    }

    /**
     * delete field
     * 
     * @return Json
     */
    public function delete() {
        header('Content-type: application/json');
    	Database::table("fields")->where("id", input("fieldId"))->delete();
        exit(json_encode(responder("success", "", "","", false)));
    }

}