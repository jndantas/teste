<?php
namespace Simcify\Controllers;

use Simcify\Str;
use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Signer;
use Simcify\Database;

class Guest {

    /**
     * Get documents view
     * 
     * @return \Pecee\Http\Response
     */
    public function open($document_key) {
        if (isset($_COOKIE['guest'])) {
          cookie("guest", '', -7);
        }
        $requestPositions = json_encode(array());
        $requestWidth = 0;
        $document = Database::table("files")->where("document_key", $document_key)->first();
        if (empty($document) || $document->is_template == "Yes") {
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
        return view('open', compact("document","lauchLabel","request","requestWidth","requestPositions"));
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
     * Sign & Edit Document
     * 
     * @return Json
     */
    public function sign() {
        header('Content-type: application/json');
        $sign = Signer::sign(input("document_key"), input("actions"), input("docWidth"), input("signing_key"), true);
        if ($sign) {
            exit(json_encode(responder("success", "Alright!", "Document successfully saved.","reload()")));
        }else{
            exit(json_encode(responder("error", "Oops!", "Something went wrong, please try again.")));
        }
        
    }

    /**
     * Decline a signing request 
     * 
     * @return Json
     */
    public function decline() {
      header('Content-type: application/json');
      $requestId = input("requestid");
        Database::table("requests")->where("id", $requestId)->update(array("status" => "Declined"));
        $request = Database::table("requests")->where("id", $requestId)->first();
        $sender = Database::table("users")->where("id", $request->sender)->first();
        $documentLink = env("APP_URL")."/document/".$request->document;
        $send = Mail::send(
            $sender->email, "Signing invitation declined by ".$request->email,
            array(
                "title" => "Signing invitation declined.",
                "subtitle" => "Click the link below to view document.",
                "buttonText" => "View Document",
                "buttonLink" => $documentLink,
                "message" => $request->email." has declined the signing invitation you had sent. Click the link above to view the document.<br><br>Cheers!<br>".env("APP_NAME")." Team"
            ),
            "withbutton"
        );
        $activity = '<span class="text-primary">'.escape($request->email).'</span> declined a signing invitation of this document.';
        Signer::keephistory($request->document, $activity, "default");
        $notification = '<span class="text-primary">'.escape($request->email).'</span> declined a signing invitation of this <a href="'.url("Document@open").$request->document.'">document</a>.';
        Signer::notification($sender->id, $notification, "decline");
        if (!$send) { exit(json_encode(responder("error", "Oops!", $send->ErrorInfo))); }
      exit(json_encode(responder("success", "Declined!", "Request declined and sender notified.","reload()")));
    }
}
