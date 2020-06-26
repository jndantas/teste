<?php
namespace Simcify\Controllers;

use Google_Client;
use Google_Service_Drive;
use Simcify\Str;
use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Signer;
use Simcify\Database;

class Document{

    /**
     * Get documents view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        return view('documents', compact("user"));
    }

    /**
     * Get documents view
     * 
     * @return \Pecee\Http\Response
     */
    public function open($document_key) {
        $user = Auth::user();
        $requestPositions = json_encode(array());
        $requestWidth = 0;
        $company = Database::table("companies")->where("id", $user->company)->first();
        $document = Database::table("files")->where("document_key", $document_key)->first();
        if (empty($document)) {
            return view('errors/404');   
        }
        if ($document->is_template == "Yes") { 
          $lauchLabel = "Manage Fields & Edit"; 
          $template_fields = json_decode($document->template_fields, true);
          $savedWidth = $template_fields[0];
          if (empty($savedWidth)) { $savedWidth = 0; }
        }else{ 
          $lauchLabel = "Sign & Edit"; 
          $template_fields = json_encode(array());
          $savedWidth = 0;
        }
        if (isset($_GET['signingKey'])) {
          $signingKey = $_GET['signingKey'];
          $request = Database::table("requests")->where("signing_key", $signingKey)->first();
          if (!empty($request->positions)) {
            $requestPositions = json_decode($request->positions, true);
            $requestWidth = $requestPositions[0];
            $requestPositions = json_encode($requestPositions, true);
            if (empty($requestWidth)) { $requestWidth = 0; }
          }
        }else{
          $request = '';
        }
        $templateFields = json_encode($template_fields, true);
        $customers = Database::table("users")->where("company", $user->company)->where("role", "user")->get();
        $team = Database::table("users")->where("company", $user->company)->where("role", "staff")->get();
        $fields = Database::table("fields")->where("user", $user->id)->where("type", "custom")->get();
        $inputfields = Database::table("fields")->where("user", $user->id)->where("type", "input")->get();
        $history = Database::table("history")->where("file", $document->document_key)->get();
        $chats = Database::table("chat")->where("file", $document_key)->leftJoin("users", "users.id","chat.sender")->get("chat.id", "chat.message", "chat.time_", "chat.sender", "users.avatar", "users.fname", "users.lname");
        return view('sign', compact("user", "document", "history", "customers", "team", "chats", "company","fields","inputfields","templateFields","savedWidth","lauchLabel","request","requestWidth","requestPositions"));
    }

    /**
     * Download a file
     * 
     * @return \Pecee\Http\Response
     */
    public function download($docId) {
        $document = Database::table("files")->where("id", $docId)->first();
        $file = config("app.storage")."files/".$document->filename;
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$document->name.'.'.$document->extension.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        flush();
        readfile($file);
        exit();
    }

    /**
     * Get documents view
     * 
     * @return \Pecee\Http\Response
     */
    public function fetch() {
        $user = Auth::user();
        $thisFolder = Database::table("folders")->where("id", input("folder"))->first();
        $folders = $documents = array();
        if (!empty($thisFolder->password)) {
            if (empty(input("password"))) {
                $protected = true;
                $incorrect = false;
                return view('extras/documents', compact("user", "protected", "incorrect"));
            }else{
                if(!hash_compare($thisFolder->password, Auth::password(input("password")))){
                    $incorrect = $protected = true;
                    return view('extras/documents', compact("user", "protected", "incorrect"));
                }
            }
        }
        if (empty($_POST['type']) || $_POST['type'] == "folders") {
            if (!empty(input("search"))) {
                $folders = Database::table("folders")->where("company", $user->company)
                                                   ->where("name","LIKE", "%".input("search")."%")->where("id",">", 1)->orderBy("id", false)->get();
            }else{
                $folders = Database::table("folders")->where("company", $user->company)
                                                   ->where("folder", input("folder"))->where("id",">", 1)->orderBy("id", false)->get();
            }
            foreach ($folders as $key => $folder) {
                if ($user->role == "user" && $folder->created_by != $user->id) {
                    unset($folders[$key]);
                }
                if ($user->id == $folder->created_by || $folder->accessibility == "Everyone") {
                    continue;
                }
                if ($folder->accessibility == "Only Me" && $user->id != $folder->created_by) {
                    unset($folders[$key]);
                }
                $giveAccess = false;
                if ($folder->accessibility == "Departments") {
                    $allowedDepartments = json_decode($folder->departments);
                    foreach ($allowedDepartments as $department) {
                        $userDepartments = Database::table("departmentmembers")->where("department", $department)->where("member", $user->id)->get("department");
                        if (count($userDepartments) > 0) {
                            $giveAccess = true;
                            break;
                        }
                    }
                }
                if (!$giveAccess) {
                    unset($folders[$key]);
                }
            }
        }
        if (empty($_POST['type']) || $_POST['type'] == "files") {
            if (!empty(input("search"))) {
                $documents = Database::table("files")->where("company", $user->company)
                                                  ->where("name","LIKE", "%".input("search")."%")->where("is_template", "No")->orderBy("id", false)->get();
            }else{
                $documents = Database::table("files")->where("company", $user->company)
                                                  ->where("folder", input("folder"))->where("is_template", "No")->orderBy("id", false)->get();
            }
            foreach ($documents as $key => $document) {
                if (!empty($_POST['status'])) {
                    if ($document->status != $_POST['status']) {
                        unset($documents[$key]);
                        continue;
                    }
                }
                if (!empty($_POST['extension'])) {
                    if ($document->extension != $_POST['extension'] && $document->extension != $_POST['extension']."x") {
                        unset($documents[$key]);
                        continue;
                    }
                }
                if ($user->role == "user" && $document->uploaded_by != $user->id) {
                    unset($documents[$key]);
                }
                if ($user->id == $document->uploaded_by || $document->accessibility == "Everyone") {
                    continue;
                }
                if ($document->accessibility == "Only Me" && $user->id != $document->uploaded_by) {
                    unset($documents[$key]);
                }
                $giveAccess = false;
                if ($document->accessibility == "Departments") {
                    $allowedDepartments = json_decode($document->departments);
                    foreach ($allowedDepartments as $department) {
                        $userDepartments = Database::table("departmentmembers")->where("department", $department)->where("member", $user->id)->get("department");
                        if (count($userDepartments) > 0) {
                            $giveAccess = true;
                            break;
                        }
                    }
                }
                if (!$giveAccess) {
                    unset($documents[$key]);
                }
            }
        }
        return view('extras/documents', compact("user", "folders", "documents"));
    }

    /**
     * Relocate file & folders
     * 
     * @return Json
     */
    public function relocate() {
        header('Content-type: application/json');
        foreach ($_POST['data'] as $data) {
            if ($data["type"] == "folder") {
                Database::table("folders")->where("id", $data["sourceid"])->update("folder", $data["destination"]);
            }else {
                Database::table("files")->where("id", $data["sourceid"])->update("folder", $data["destination"]);
            }
        }
        exit(json_encode(responder("success", "", "","", false)));
    }

    /**
     * Create a folder
     * 
     * @return Json
     */
    public function createfolder() {
        header('Content-type: application/json');
        $user = Auth::user();
        if ($user->company == 0) {
            $folders = Database::table("folders")->where("name", input("name"))->where("folder", input("folder"))->first();
        }else{
            $folders = Database::table("folders")->where("name", input("name"))->where("folder", input("folder"))->where("company", $user->company)->first();
        }
        if (!empty($folders)) {
            exit(json_encode(responder("error", "Already Exists!", "Folder name '".input("name")."' already exists.")));
        }
        if ($user->role == "user") {
            $accessibility = "Only Me";
        }else{
            $accessibility = "Everyone";
        }
        $data = array(
                        "company" => $user->company,
                        "created_by" => $user->id,
                        "name" => input("name"),
                        "accessibility" => $accessibility,
                        "folder" => input("folder"),
                    );
        Database::table("folders")->insert($data);
        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
    }

    /**
     * update/rename a folder
     * 
     * @return Json
     */
    public function updatefolder() {
        header('Content-type: application/json');
        $user = Auth::user();
        if ($user->company == 0) {
            $folders = Database::table("folders")->where("name", input("foldername"))->where("folder", input("folder"))->first();
        }else{
            $folders = Database::table("folders")->where("name", input("foldername"))->where("folder", input("folder"))->where("company", $user->company)->first();
        }
        if (!empty($folders)) {
            exit(json_encode(responder("error", "Already Exists!", "Folder name '".input("foldername")."' already exists.")));
        }
        Database::table("folders")
                        ->where("id", input("folderid"))
                        ->where("company", $user->company)
                        ->update("name", escape(input("foldername")));
        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
    }

    /**
     * update/rename a file
     * 
     * @return Json
     */
    public function updatefile() {
        header('Content-type: application/json');
        $user = Auth::user();
        if ($user->company == 0) {
            $files = Database::table("files")->where("name", input("filename"))->where("folder", input("folder"))->first();
        }else{
            $files = Database::table("files")->where("name", input("filename"))->where("folder", input("folder"))->where("company", $user->company)->first();
        }
        if (!empty($files)) {
            exit(json_encode(responder("error", "Already Exists!", "File '".input("filename")."' already exists.")));
        }
        Database::table("files")
                        ->where("id", input("fileid"))
                        ->update("name", escape(input("filename")));
        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
    }

    /**
     * Duplicate a file
     * 
     * @return Json
     */
    public function duplicate() {
        header('Content-type: application/json');
        Signer::duplicate(input("file"));
        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
    }

    /**
     * Update folder accessibility view
     * 
     * @return \Pecee\Http\Response
     */
    public function updatefolderaccessview() {
        $user = Auth::user();
        $folder = Database::table("folders")
                                           ->where("company", $user->company)
                                           ->where("id", input("folder"))
                                           ->first();
        $departments = Database::table("departments")->where("company", $user->company)->get();
        if ($folder->accessibility == "Departments") {
            $allowedDepartments = json_decode($folder->departments);
        }else{
            $allowedDepartments = array();
        }
        return view('extras/folderaccess', compact("user", "folder", "departments","allowedDepartments"));
    }

    /**
     * Update folder accessibility 
     * 
     * @return Json
     */
    public function updatefolderaccess() {
        if (input("accessibility") == "Departments") {
            $departments = json_encode($_POST['departments']);
        }else{
            $departments = "";
        }
        $data = array(
                        "accessibility" => input("accessibility"),
                        "departments" => $departments
                    );
        $user = Auth::user();
        $folder = Database::table("folders")
                                           ->where("company", $user->company)
                                           ->where("id", input("folderid"))
                                           ->update($data);
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright!", "Folder accessibility updated.","hideSharedModal();")));
    }

    /**
     * Update file accessibility view
     * 
     * @return \Pecee\Http\Response
     */
    public function updatefileaccessview() {
        $user = Auth::user();
        $file = Database::table("files")
                                           ->where("id", input("file"))
                                           ->first();
        $departments = Database::table("departments")->where("company", $user->company)->get();
        if ($file->accessibility == "Departments") {
            $allowedDepartments = json_decode($file->departments);
        }else{
            $allowedDepartments = array();
        }
        return view('extras/fileaccess', compact("user", "file", "departments","allowedDepartments"));
    }

    /**
     * Update file accessibility 
     * 
     * @return Json
     */
    public function updatefileaccess() {
        if (input("accessibility") == "Departments") {
            $departments = json_encode($_POST['departments']);
        }else{
            $departments = "";
        }
        $data = array(
                        "accessibility" => input("accessibility"),
                        "departments" => $departments
                    );
        $user = Auth::user();
        $folder = Database::table("files")
                                           ->where("id", input("fileid"))
                                           ->update($data);
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright!", "File accessibility updated.","hideSharedModal();")));
    }

    /**
     * Update folder protection view
     * 
     * @return \Pecee\Http\Response
     */
    public function updatefolderprotectview() {
        $user = Auth::user();
        $folder = Database::table("folders")->where("company", $user->company)->where("id", input("folder"))->first();
        return view('extras/folderprotect', compact("user", "folder"));
    }

    /**
     * Update folder protection
     * 
     * @return Json
     */
    public function updatefolderprotect() {
        header('Content-type: application/json');
        $user = Auth::user();
        $folder = Database::table("folders")->where("company", $user->company)->where("id", input("folderid"))->first();
        if (!empty($folder->password)) {
            if(!hash_compare($folder->password, Auth::password(input("current")))){
                exit(json_encode(responder("error", "Oops!", "Incorrect current password.")));
            }
        }
        if (input("password") == "remove") {
            $password = "";
        }else{
            $password = Auth::password(input("password"));
        }
        Database::table("folders")->where("company", $user->company)->where("id", input("folderid"))->update(array("password" => $password));
        exit(json_encode(responder("success", "Alright!", "Folder protection updated.","reload();")));
    }

    /**
     * delete a folder
     * 
     * @return Json
     */
    public function deletefolder() {
        header('Content-type: application/json');
        $delete = Signer::deletefolder(input("folder"));
        if ($delete) {
            exit(json_encode(responder("success", "", "","deleted(true);", false)));
        }else{
            exit(json_encode(responder("error", "Oops!", "Delete failed, please try again.","deleted(false);", true, "toastr")));
        }
    }

    /**
     * delete a file
     * 
     * @return Json
     */
    public function deletefile() {
        header('Content-type: application/json');
        $delete = Signer::deletefile(input("file"));
        if ($delete) {
            if (isset($_POST['source'])) {
                exit(json_encode(responder("success", "", "","redirect('".env("APP_URL")."/documents');", false)));
            }else{
                exit(json_encode(responder("success", "", "","deleted(true);", false)));
            }
        }else{
            exit(json_encode(responder("error", "Oops!", "Delete failed, please try again.","deleted(false);", true, "toastr")));
        }
    }


    /**
     * delete multiple items
     * 
     * @return Json
     */
    public function delete() {
        header('Content-type: application/json');
        foreach ($_POST['data'] as $data) {
            if ($data["type"] == "folder") {
                Signer::deletefolder($data["itemid"]);
            }else{
                Signer::deletefile($data["itemid"]);
            }
        }
        exit(json_encode(responder("success", "", "","", false)));
    }

    /**
     * Upload a file
     * 
     * @return Json
     */
    public function uploadfile() {
        header('Content-type: application/json');
        $user = Auth::user();
        $data = array(
                        "company" => $user->company,
                        "uploaded_by" => $user->id,
                        "name" => input("name"),
                        "folder" => input("folder"),
                        "file" => $_FILES['file'],
                        "is_template" => "No",
                        "source" => "form",
                        "document_key" => Str::random(32),
                        "activity" => 'File uploaded by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.'
                    );
        $upload = Signer::upload($data);
        if ($upload['status'] == "success") {
            exit(json_encode(responder("success", "", "","documentsCallback()", false)));
        }else{
            exit(json_encode(responder("error", "Oops!", $upload['message'])));
        }
    }

    /**
     * Save file imported from Dropbox
     * 
     * @return Json
     */
    public function dropboximport() {
        header('Content-type: application/json');
        $user = Auth::user();
        $data = array(
        				"company" => $user->company,
        				"uploaded_by" => $user->id,
        				"name" => input("name"),
        				"folder" => input("folder"),
        				"file" => input("url"),
        				"is_template" => "No",
        				"source" => "url",
        				"document_key" => Str::random(32),
        				"activity" => 'File Imported from Dropbox by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.'
        			);
        $upload = Signer::upload($data);
        if ($upload['status'] == "success") {
	        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
        }else{
	        exit(json_encode(responder("error", "Oops!", $upload['message'])));
        }
        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
    }

    /**
     * Save file imported from Google Drive
     * 
     * @return Json
     */
    public function googledriveimport() {
        $fileName = Str::random(32).".pdf";
        $outputFile = config("app.storage")."/files/".$fileName;
        putenv('GOOGLE_APPLICATION_CREDENTIALS=uploads/credentials/keys.json');
        $client = new Google_Client();
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->useApplicationDefaultCredentials();
        $service = new Google_Service_Drive($client);
        try {
            $content = $service->files->export(input('fileId'), 'application/pdf', array("alt" => "media"));
        }
        catch(\Exception $e) {
            header('Content-type: application/json');
            exit(json_encode(responder("error", "Oops!", $e->getMessage())));
        }
        $headers = $content->getHeaders();
        foreach ($headers as $name => $values) {
            header($name . ': ' . implode(', ', $values));
        }
        $f = fopen($outputFile, 'w');
        fwrite($f, $content->getBody());
        fclose($f);
        $user = Auth::user();
        $data = array(
        				"company" => $user->company,
        				"uploaded_by" => $user->id,
        				"name" => input("name"),
        				"folder" => input("folder"),
        				"file" => $fileName,
        				"is_template" => "No",
        				"source" => "googledrive",
                        "document_key" => Str::random(32),
        				"size" => round(filesize($outputFile) / 1000),
        				"activity" => 'File Imported from Google Drive by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.'
        			);
        $upload = Signer::upload($data);
        header('Content-type: application/json');
        if ($upload['status'] == "success") {
	        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
        }else{
	        exit(json_encode(responder("error", "Oops!", $upload['message'])));
        }
        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
    }


    /**
     * Restore file version
     * 
     * @return Json
     */
    public function restore() {
        $user = Auth::user();
        $document = Database::table("files")->where("id", input("file"))->first();
        Signer::deletefile($document->filename, "original");
        copy(config("app.storage")."copies/".$document->filename, config("app.storage")."files/".$document->filename);
        $data = array( "status" => "Unsigned", "sign_reason" => "", "editted" => "No" );
        Database::table("files")->where("document_key", $document->document_key)->update($data);
        $activity = 'File restored to orginal version by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>';
        Signer::keephistory($document->document_key, $activity);
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Restored!", "Original version successfully restored.","reload()")));
    }

    /**
     * Replace a file
     * 
     * @return Json
     */
    public function replace() {
        header('Content-type: application/json');
        $user = Auth::user();
        $document_key = input("document_key");
        $document = Database::table("files")->where("document_key", $document_key)->first();
        if(env("ALLOW_NON_PDF") == "Enabled"){
            $allowedExtensions = "pdf, doc, docx, ppt, pptx, xls, xlsx";
        }else{
            $allowedExtensions = "pdf";
        }
        $upload = File::upload(
            $_FILES['file'], 
            "files",
            array(
                "allowedExtesions" => $allowedExtensions,
            )
        );
        if ($upload['status'] == "success") {
            Signer::deletefile($document->filename, true);
            Signer::keepcopy($upload['info']['name']);
            $data = array(
                            "status" => "Unsigned",
                            "editted" => "No",
                            "sign_reason" => "",
                            "filename" => $upload['info']['name'],
                            "size" => $upload['info']['size'],
                            "extension" => $upload['info']['extension']
                        );
            Database::table("files")->where("document_key", $document_key)->update($data);
            $activity = 'File replaced with a new one by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.';
            Signer::keephistory($document_key, $activity);
            exit(json_encode(responder("success", "Replaced!", "File successfully replaced.","reload()")));
        }else{
            exit(json_encode(responder("error", "Oops!", $upload['message'])));
        }
    }

    /**
     * Protect a pdf file
     * 
     * @return Json
     */
    public function protect() {
        header('Content-type: application/json');
        if(!isset($_POST['permission']) && !isset($_POST['setPassword'])){
            exit(json_encode(responder("warning", "Oops!", "No protection mode selected..")));
        }
        if(isset($_POST['permission'])){
            $permission = $_POST['permission'];
        }else{
            $permission = array();
        }
        if(isset($_POST['setPassword'])){
            $userpassword = $_POST['userpassword'];
            $ownerpassword = $_POST['ownerpassword'];
        }else{
            $userpassword = null;
            $ownerpassword = null;
        }
        $protected = Signer::protect($permission, $userpassword, $ownerpassword, input("document_key"));
        if ($protected) {
            exit(json_encode(responder("success", "Alright!", "Document protection activated.","reload()")));
        }else{
            exit(json_encode(responder("error", "Oops!", "Signer could not modify this file, the file could be protected.")));
        }
    }

    /**
     * Send file
     * 
     * @return Json
     */
    public function send() {
        header('Content-type: application/json');
        $user = Auth::user();
        $document_key = input("document_key");
        $document = Database::table("files")->where("document_key", $document_key)->first();
        $emails = explode(",", input("receivers"));
        foreach ($emails as $email) {
            $send = Mail::send(
                $email,
                "You have received a document from ".$user->fname." ".$user->lname,
                array(
                    "message" => "Hello there,<br><br>You have received a file from ".$user->fname." ".$user->lname.".<br><strong>Message:</strong> ".input("message")."<br><br>Cheers!<br>".env("APP_NAME")." Team"
                ),
                "basic",
                null,
                array($document->name.'.'.$document->extension => config("app.storage")."files/".$document->filename)
            );

            if (!$send) {
                exit(json_encode(responder("error", "Oops!", $send->ErrorInfo)));
            }
        }
        exit(json_encode(responder("success", "Sent!", "File successfully sent.","reload()")));
    }

    /**
     * Convert file to PDF
     * 
     * @return Json
     */
    public function convert() {
        header('Content-type: application/json');
        $convert = Signer::convert(input("document_key"));
        if ($convert['status'] == "success") {
            exit(json_encode(responder("success", "Converted!", "File successfully converted.","reload()")));
        }else{
            exit(json_encode(responder("error", "Failed!", $convert['message'])));
        }
        
    }

    /**
     * Sign & Edit Document
     * 
     * @return Json
     */
    public function sign() {
        header('Content-type: application/json');
        $sign = Signer::sign(input("document_key"), input("actions"), input("docWidth"), input("signing_key"));
        if ($sign) {
            exit(json_encode(responder("success", "Alright!", "Document successfully saved.","reload()")));
        }else{
            exit(json_encode(responder("error", "Oops!", "Something went wrong, please try again.")));
        }
        
    }
}
