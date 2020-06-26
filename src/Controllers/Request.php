<?php
namespace Simcify\Controllers;

use Simcify\Str;
use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Signer;
use Simcify\Database;

class Request{

    /**
     * Get requests view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->company == 0) {
        	$requestsData = Database::table("requests")->where("sender", $user->id)->get();
        }else{
        	$requestsData = Database::table("requests")->where("company", $user->company)->get();
        }
        $requests = array();
        foreach ($requestsData as $request) {
        	if (!empty($request->receiver)) {
        		$receiver = Database::table("users")->where("id" , $request->receiver)->first();
        		if (!empty($receiver)) {
        			$receiverInfo = $receiver;
        		}
        	}else{
    			$receiver = Database::table("users")->where("email" , $request->email)->first();
        		if (!empty($receiver)) {
        			$receiverInfo = $receiver;
        		}else{
        			$receiverInfo = $request->email;
        		}
    		}
        	$requests[] = array(
                                                "data" => $request,
                                                "file" => Database::table("files")->where("document_key" , $request->document)->first(),
                                                "sender" => Database::table("users")->where("id" , $request->sender)->first(),
                                                "receiver" => $receiverInfo
                                            );
        }
        
        return view('requests', compact("user", "requests"));
    }

    /**
     * Send signing request
     * 
     * @return Json
     */
    public function send() {
    	header('Content-type: application/json');
    	$user = Auth::user();
    	$document = Database::table("files")->where("document_key", input("document_key"))->first();
    	$emails = json_decode($_POST['emails'], true);
    	$message = $_POST['message'];
    	$documentKey = $document->document_key;
    	$duplicate = $_POST['duplicate'];
    	$activity = 'Signing request sent to <span class="text-primary">'.implode(", ", $emails).'</span> by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.';
    	if (!empty($_POST['positions'])) {
	    	$positions = json_decode($_POST['positions'], true);
	    	if (input("docWidth") != "set") { array_unshift($positions, input("docWidth")); }
	    	$positions = json_encode($positions);
    	}else{
    		$positions = '';
    	}
    	foreach($emails as $email){
    		$signingKey = Str::random(32);
    		if ($duplicate == "Yes" || $document->is_template == "Yes") {
    			$duplicateDocId = Signer::duplicate($document->id, $document->name." (".$email.")");
    			$duplicateDoc = Database::table("files")->where("id", $duplicateDocId)->first();
    			$documentKey = $duplicateDoc->document_key;
                Database::table("files")->where("id", $duplicateDoc->id)->update(array("is_template" => "No"));
    			$duplicateActivity = 'Signing request sent to <span class="text-primary">'.escape($email).'</span> by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.';
    			Signer::keephistory($documentKey, $duplicateActivity, "default");
    		}
			$signingLink = env("APP_URL")."/document/".$documentKey."?signingKey=".$signingKey;
			$receiverData = Database::table("users")->where("email", $email)->first();
			if (!empty($receiverData)) { $receiver = $receiverData->id; }else{ $receiver = 0; }
			$request = array( "company" => $user->company, "document" => $documentKey, "signing_key" => $signingKey, "positions" => $positions, "email" => $email, "sender" => $user->id, "receiver" => $receiver );
			Database::table("requests")->insert($request);
    		$send = Mail::send(
                $email, $user->fname." ".$user->lname." has invited you to sign a document",
                array(
                    "title" => "Document Signing invite",
                    "subtitle" => "Click the link below to respond to the invite.",
                    "buttonText" => "Sign Now",
                    "buttonLink" => $signingLink,
                    "message" => "You have been invited to sign a document by ".$user->fname." ".$user->lname.". Click the link above to respond to the invite.<br><strong>Message:</strong> ".input("message")."<br><br>Cheers!<br>".env("APP_NAME")." Team"
                ),
                "withbutton"
            );
            if (!$send) { exit(json_encode(responder("error", "Oops!", $send->ErrorInfo))); }
    	}
    	Signer::keephistory($document->document_key, $activity, "default");
    	exit(json_encode(responder("success", "Sent!", "Request successfully sent.","reload()")));
    }

    /**
     * Delete signing request
     * 
     * @return Json
     */
    public function delete() {
    	header('Content-type: application/json');
    	$requestId = input("requestid");
    	Database::table("requests")->where("id", $requestId)->delete();
    	exit(json_encode(responder("success", "Deleted!", "Request successfully deleted.","reload()")));
    }

    /**
     * Cancel signing request
     * 
     * @return Json
     */
    public function cancel() {
    	header('Content-type: application/json');
    	$requestId = input("requestid");
    	Database::table("requests")->where("id", $requestId)->update(array("status" => "Cancelled"));
    	exit(json_encode(responder("success", "Cancelled!", "Request successfully cancelled.","reload()")));
    }

    /**
     * Send a signing request reminder
     * 
     * @return Json
     */
    public function remind() {
        header('Content-type: application/json');
        $requestId = input("requestid");
        $user = Auth::user();
        $request = Database::table("requests")->where("id", $requestId)->first();
        $signingLink = env("APP_URL")."/document/".$request->document."?signingKey=".$request->signing_key;
        $send = Mail::send(
            $request->email, "Signing invitation reminder from ".$user->fname." ".$user->lname,
            array(
                "title" => "Signing invitation reminder.",
                "subtitle" => "Click the link below to respond to the invite.",
                "buttonText" => "Sign Now",
                "buttonLink" => $signingLink,
                "message" => "You have been invited to sign a document by ".$user->fname." ".$user->lname.". Click the link above to respond to the invite.<br><strong>Message:</strong> ".input("message")."<br><br>Cheers!<br>".env("APP_NAME")." Team"
            ),
            "withbutton"
        );
        $activity = '<span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span> sent a signing reminder to <span class="text-primary">'.escape($request->email).'</span>.';
        Signer::keephistory($request->document, $activity, "default");
        if (!$send) { exit(json_encode(responder("error", "Oops!", $send->ErrorInfo))); }
        exit(json_encode(responder("success", "Sent!", "Reminder successfully send.","reload()")));
    }

    /**
     * Decline a signing request 
     * 
     * @return Json
     */
    public function decline() {
    	header('Content-type: application/json');
    	$requestId = input("requestid");
    	$user = Auth::user();
        Database::table("requests")->where("id", $requestId)->update(array("status" => "Declined"));
        $request = Database::table("requests")->where("id", $requestId)->first();
        $sender = Database::table("users")->where("id", $request->sender)->first();
        $documentLink = env("APP_URL")."/document/".$request->document;
        $send = Mail::send(
            $sender->email, "Signing invitation declined by ".$user->fname." ".$user->lname,
            array(
                "title" => "Signing invitation declined.",
                "subtitle" => "Click the link below to view document.",
                "buttonText" => "View Document",
                "buttonLink" => $documentLink,
                "message" => $user->fname." ".$user->lname." has declined the signing invitation you had sent. Click the link above to view the document.<br><br>Cheers!<br>".env("APP_NAME")." Team"
            ),
            "withbutton"
        );
        $activity = '<span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span> declined a signing invitation of this document.';
        Signer::keephistory($request->document, $activity, "default");
        $notification = '<span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span> declined a signing invitation of this <a href="'.url("Document@open").$request->document.'">document</a>.';
        Signer::notification($sender->id, $notification, "decline");
        if (!$send) { exit(json_encode(responder("error", "Oops!", $send->ErrorInfo))); }
    	exit(json_encode(responder("success", "Declined!", "Request declined and sender notified.","reload()")));
    }

}
