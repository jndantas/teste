<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Dashboard{

    /**
     * Get dashboard view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
    	$user = Auth::user();
    	if ($user->role == "user") {
	        $fileLimit = env("PERSONAL_FILE_LIMIT");
	        $diskLimit = env("PERSONAL_DISK_LIMIT");
	        $diskUsage = Database::table("files")->where("uploaded_by" , $user->id)->sum("size", "size")[0]->size;
	        $fileUsage = Database::table("files")->where("uploaded_by" , $user->id)->count("id", "files")[0]->files;
	        $folders = Database::table("folders")->where("created_by" , $user->id)->count("id", "folders")[0]->folders;

	        // signed vs unsigned
	        $signed = Database::table("files")->where("uploaded_by" , $user->id)->where("status" , "Signed")->count("id", "total")[0]->total;
	        $unsigned = Database::table("files")->where("uploaded_by" , $user->id)->where("status" , "Unsigned")->count("id", "total")[0]->total;

	    	// file types stats
	    	$myPdf = Database::table("files")->where(array("extension" => "pdf", "uploaded_by" => $user->id), "=")
					    	    ->count("id", "files")[0]->files;
	    	$myWord = Database::table("files")->where(array("extension" => "doc", "uploaded_by" => $user->id), "=")
	    						->orWhere("extension","docx")->Where("uploaded_by", $user->id)->count("id", "files")[0]->files;
	    	$myExcel = Database::table("files")->where(array("extension" => "xls", "uploaded_by" => $user->id), "=")
	    						->orWhere("extension","xlsx")->Where("uploaded_by", $user->id)->count("id", "files")[0]->files;
	    	$myPpt = Database::table("files")->where(array("extension" => "ppt", "uploaded_by" => $user->id), "=")
	    						->orWhere("extension","pptx")->Where("uploaded_by", $user->id)->count("id", "files")[0]->files;

	    	// pending signing requests 
	    	$pendingRequests = Database::table("requests")->where("sent_by" , $user->id)->where("status" , "Pending")->count("id", "total")[0]->total;
    	}else{
	        $fileLimit = env("BUSINESS_FILE_LIMIT");
	        $diskLimit = env("BUSINESS_DISK_LIMIT");
	        $diskUsage = Database::table("files")->where("company" , $user->company)->sum("size", "size")[0]->size;
	        $fileUsage = Database::table("files")->where("company" , $user->company)->count("id", "files")[0]->files;
	        $folders = Database::table("folders")->where("company" , $user->company)->count("id", "folders")[0]->folders;

	        // signed vs unsigned
	        $signed = Database::table("files")->where("company" , $user->company)->where("status" , "Signed")->count("id", "total")[0]->total;
	        $unsigned = Database::table("files")->where("company" , $user->company)->where("status" , "Unsigned")->count("id", "total")[0]->total;

	    	// file types stats
	    	$myPdf = Database::table("files")->where(array("extension" => "pdf", "company" => $user->company), "=")
					    	    ->count("id", "files")[0]->files;
	    	$myWord = Database::table("files")->where(array("extension" => "doc", "uploaded_by" => $user->id), "=")
	    						->orWhere("extension","docx")->Where("company", $user->company)->count("id", "files")[0]->files;
	    	$myExcel = Database::table("files")->where(array("extension" => "xls", "uploaded_by" => $user->id), "=")
	    						->orWhere("extension","xlsx")->Where("company", $user->company)->count("id", "files")[0]->files;
	    	$myPpt = Database::table("files")->where(array("extension" => "ppt", "uploaded_by" => $user->id), "=")
	    						->orWhere("extension","pptx")->Where("company", $user->company)->count("id", "files")[0]->files;

	    	// pending signing requests 
	    	$pendingRequests = Database::table("requests")->where("company" , $user->company)->where("status" , "Pending")->count("id", "total")[0]->total;
    	}

    	if ($user->role == "superadmin") {
	    	// system usage stats
	    	$systemDisk = Database::table("files")->sum("size", "size")[0]->size;
	    	$systemFiles = Database::table("files")->count("id", "files")[0]->files;
	    	$systemUsers = Database::table("users")->count("id", "users")[0]->users;

	    	// account type stats
	    	$businessAccounts = Database::table("companies")->where("id",">", 0)->count("id", "companies")[0]->companies;
	    	$personalAccounts = Database::table("users")->where("role", "user")->where("company", "0")->count("id", "users")[0]->users;

	    	// file types stats
	    	$totalPdf = Database::table("files")->where("extension", "pdf")->count("id", "files")[0]->files;
	    	$totalWord = Database::table("files")->where("extension", "doc")->orWhere("extension", "docx")->count("id", "files")[0]->files;
	    	$totalExcel = Database::table("files")->where("extension", "xls")->orWhere("extension", "xlsx")->count("id", "files")[0]->files;
	    	$totalPpt = Database::table("files")->where("extension", "ppt")->orWhere("extension", "pptx")->count("id", "files")[0]->files;

	        return view('dashboard', compact("user","fileUsage","diskUsage","diskLimit","fileLimit","folders","pendingRequests","signed","unsigned","myPdf","myWord","myExcel","myPpt","systemDisk","systemFiles","systemUsers","businessAccounts","personalAccounts","totalPdf","totalWord","totalExcel","totalPpt"));
    	}else{
	        return view('dashboard', compact("user","fileUsage","diskUsage","diskLimit","fileLimit","folders","pendingRequests","signed","unsigned","myPdf","myWord","myExcel","myPpt"));
    	}

    }

}
