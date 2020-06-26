<?php
namespace Simcify\Controllers;

use Google_Client;
use Google_Service_Drive;
use Simcify\Str;
use Simcify\File;
use Simcify\Auth;
use Simcify\Signer;
use Simcify\Database;

class Template{

    /**
     * Get templates view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
    	$user = Auth::user();
        return view('templates', compact("user"));
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
                        "folder" => 1,
                        "file" => $_FILES['file'],
                        "is_template" => "Yes",
                        "source" => "form",
                        "document_key" => Str::random(32),
                        "activity" => 'Template uploaded by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.'
                    );
        $upload = Signer::upload($data);
        if ($upload['status'] == "success") {
            exit(json_encode(responder("success", "", "","documentsCallback()", false)));
        }else{
            exit(json_encode(responder("error", "Oops!", $upload['message'])));
        }
    }

    /**
     * Create a template version of a file
     * 
     * @return Json
     */
    public function create() {
        header('Content-type: application/json');
        $document = Database::table("files")->where("document_key", input("document_key"))->first();
        $templateId = Signer::duplicate($document->id);
        Database::table("files")->where("id", $templateId)->update(array("is_template" => "Yes"));
        $template = Database::table("files")->where("id", $templateId)->first();
        $url = url("Document@open").$template->document_key;
        exit(json_encode(responder("success", "Created!", "Template created, click continue to view.","redirect('".$url."')")));
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
        				"folder" => 1,
        				"file" => input("url"),
        				"is_template" => "Yes",
        				"source" => "url",
        				"document_key" => Str::random(32),
        				"activity" => 'Template Imported from Dropbox by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.'
        			);
        $upload = Signer::upload($data);
        if ($upload['status'] == "success") {
	        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
        }else{
	        exit(json_encode(responder("error", "Oops!", $upload['message'])));
        }
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
        				"folder" => 1,
        				"file" => $fileName,
        				"is_template" => "Yes",
        				"source" => "googledrive",
                        "document_key" => Str::random(32),
        				"size" => round(filesize($outputFile) / 1000),
        				"activity" => 'Template Imported from Google Drive by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.'
        			);
        $upload = Signer::upload($data);
        header('Content-type: application/json');
        if ($upload['status'] == "success") {
	        exit(json_encode(responder("success", "", "","documentsCallback()", false)));
        }else{
	        exit(json_encode(responder("error", "Oops!", $upload['message'])));
        }
    }

    /**
     * Get documents view
     * 
     * @return \Pecee\Http\Response
     */
    public function fetch() {
        $user = Auth::user();
        $folders = array();
        $documents = Database::table("files")
                                          ->where("company", $user->company)
                                          ->where("folder", 1)
                                          ->where("is_template", "Yes")
                                          ->orderBy("id", false)
                                          ->get();
        foreach ($documents as $key => $document) {
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
        return view('extras/documents', compact("user", "folders", "documents"));
    }

}
