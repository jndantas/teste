<?php 
namespace Simcify;

require_once 'TCPDF/tcpdf.php';
require_once 'TCPDF/tcpdi.php';
use TCPDF;
use TCPDI;
use Simcify\Str;
use Simcify\File;
use Simcify\Auth;
use Simcify\Database;
use \CloudConvert\Api;

class PDF extends TCPDI {
    var $_tplIdx;
    var $numPages;

    function Header() {}

    function Footer() {}

}

class Signer {
    
    /**
     * Upload file
     * 
     * @param   array $data
     * @return  true
     */
    public static function upload($data) {
        $user = Auth::user();
        // get usage data
        if ($user->role == "user") {
            $fileUsage = Database::table("files")->where("uploaded_by" , $user->id)->count("id", "files")[0]->files;
            $diskUsage = Database::table("files")->where("uploaded_by" , $user->id)->sum("id", "size")[0]->size / 1000;
            // check file usage limits
            if ($fileUsage > env("PERSONAL_FILE_LIMIT")) {
                return responder("error", "Limit Exceeded!", "You have exceeded your limit of ".env("PERSONAL_FILE_LIMIT")." files.");
            }
            // check disk usage limits
            if ($diskUsage > env("PERSONAL_DISK_LIMIT")) {
                return responder("error", "Limit Exceeded!", "You have exceeded your limit of ".env("PERSONAL_DISK_LIMIT")." MBs.");
            }
        }else{
            $fileUsage = Database::table("files")->where("company" , $user->company)->count("id", "files")[0]->files;
            $diskUsage = Database::table("files")->where("company" , $user->company)->sum("id", "size")[0]->size / 1000;
            // check file usage limits
            if ($fileUsage > env("BUSINESS_FILE_LIMIT")) {
                return responder("error", "Limit Exceeded!", "You have exceeded your limit of ".env("BUSINESS_FILE_LIMIT")." files.");
            }
            // check disk usage limits
            if ($diskUsage > env("BUSINESS_DISK_LIMIT")) {
                return responder("error", "Limit Exceeded!", "You have exceeded your limit of ".env("BUSINESS_DISK_LIMIT")." MBs.");
            }
        }
        if ($user->company == 0) {
            $files = Database::table("files")->where("name", $data["name"])->where("folder", $data["folder"])->first();
        }else{
            $files = Database::table("files")->where("name", $data["name"])->where("folder", $data["folder"])->where("company", $user->company)->first();
        }
        if (!empty($files) && $data["source"] == "form") {
            return responder("error", "Already Exists!", "File name '".$data["name"]."' already exists.");
        }
        if(env("ALLOW_NON_PDF") == "Enabled"){
            $allowedExtensions = "pdf, doc, docx, ppt, pptx, xls, xlsx";
        }else{
            $allowedExtensions = "pdf";
        }
        if ($data['source'] == "googledrive") {
            $upload = array(
                                "status" => "success",
                                "info" => array(
                                                    "name" => $data['file'],
                                                    "size" => $data['size'],
                                                    "extension" => "pdf"
                                                )
                            );
        }else{
            $upload = File::upload(
                $data['file'], 
                "files",
                array(
                    "source" => $data['source'],
                    "allowedExtensions" => $allowedExtensions
                )
            );
        }

        if ($upload['status'] == "success") {
            self::keepcopy($upload['info']['name']);
            $data["filename"] = $upload['info']['name'];
            $data["size"] = $upload['info']['size'];
            $data["extension"] = $upload['info']['extension'];
            $activity = $data['activity'];
            unset($data['file'], $data['source'], $data['activity']);
            Database::table("files")->insert($data);
            $documentId = Database::table("files")->insertId();
            $document = Database::table("files")->where("id", $documentId)->get("document_key");
            Database::table("history")->insert(array("company" => $data['company'], "file" => $document[0]->document_key, "activity" => $activity, "type" => "default"));
            return responder("success", "Upload Complete", "File successfully uploaded.");
        }else{
            return responder("error", "Oops!", $upload['message']);
        }
    }
    
    /**
     * Duplicate file
     * 
     * @param   int $data
     * @return  true
     */
    public static function duplicate($file, $duplicateName = '') {
        $document = Database::table("files")->where("id", $file)->first();
    	$user = Auth::user();
        $fileName = Str::random(32).".".$document->extension;
        copy(config("app.storage")."files/".$document->filename, config("app.storage")."files/".$fileName);
        self::keepcopy($fileName);
        if(empty($duplicateName)){ $duplicateName = $document->name." (Copy)"; }
        $activity = 'File Duplicated from '.escape($document->name).' by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.';
        $data = array(
                        "company" => $user->company,
                        "uploaded_by" => $user->id,
                        "name" => $duplicateName,
                        "folder" => $document->folder,
                        "filename" => $fileName,
                        "extension" => $document->extension,
                        "size" => $document->size,
                        "status" => $document->status,
                        "is_template" => $document->is_template,
                        "document_key" => Str::random(32)
                    );
        Database::table("files")->insert($data);
        $documentId = Database::table("files")->insertId();
        $document = Database::table("files")->where("id", $documentId)->get("document_key");
        Database::table("history")->insert(array("company" => $data['company'], "file" => $document[0]->document_key, "activity" => $activity, "type" => "default"));
        return $documentId;
    }
    
    /**
     * Copy file
     * 
     * @param   string $filename
     * @return  true
     */
    public static function keepcopy($filename) {
        copy(config("app.storage")."files/".$filename, config("app.storage")."copies/".$filename);
        return true;
    }
    
    /**
     * Copy file
     * 
     * @param   string $filename
     * @return  true
     */
    public static function renamecopy($fileName, $newName) {
        rename(config("app.storage")."copies/".$fileName, config("app.storage")."copies/".$newName);
        return true;
    }
    
    /**
     * Delete a folder
     * 
     * @param   string|int $folderId
     * @return  true
     */
    public static function deletefolder($folderId) {
        $foldersToDelete = $filesToDelete = array();
        $user = Auth::user();
        $thisFolder = Database::table("folders")->where("id", $folderId)->first();
        if ($user->company != $thisFolder->company) {
            return false;
        }
        $folders = Database::table("folders")
                         ->where("folder", $folderId)
                         ->get();
        foreach ($folders as $folder) {
            $foldersToDelete[] = $folder->id;
            $folderFiles = Database::table("files")->where("folder", $folder->id)->get();
            foreach ($folderFiles as $file) {
                self::deletefile($file->filename, true);
            }
            self::deletefolder($folder->id);
        }
        $folderFiles = Database::table("files")->where("folder", $folderId)->get();
        foreach ($folderFiles as $file) {
            self::deletefile($file->filename, true);
        }
        Database::table("folders")->where("id", $folderId)->delete();
        return true;
    }
    
    /**
     * Delete a file
     * 
     * @param   int $fileId
     * @return  true
     */
    public static function deletefile($fileId, $actualFile = false) {
        if (!$actualFile) {
            $user = Auth::user();
            $thisFile = Database::table("files")->where("id", $fileId)->first();
            if ($user->company != $thisFile->company) {
                return false;
            }
            File::delete($thisFile->filename, "files");
            File::delete($thisFile->filename, "copies");
            Database::table("files")->where("id", $thisFile->id)->delete();
        }else{
            if ($actualFile == "original") {
                File::delete($fileId, "files");
            }else{
                File::delete($fileId, "files");
                File::delete($fileId, "copies");
            }
        }
    	return true;
    }
    
    /**
     * Record file history
     * 
     * @param   string $document_key
     * @param   string $activity
     * @param   string $type
     * @return  true
     */
    public static function keephistory($document_key, $activity, $type = "default") {
        $document = Database::table("files")->where("document_key", $document_key)->first();
        Database::table("history")->insert(array("company" => $document->company, "file" => $document_key, "activity" => $activity, "type" => $type));
        return true;
    }
    
    /**
     * Save notifications
     * 
     * @param   int $user
     * @param   string $notification
     * @param   string $type
     * @return  true
     */
    public static function notification($user, $notification, $type = "warning") {
        Database::table("notifications")->insert(array("user" => $user, "message" => $notification, "type" => $type));
        return true;
    }
    
    /**
     * Convert file to PDF
     * 
     * @param   string $document_key
     * @return  array
     */
    public static function convert($document_key) {
        $user = Auth::user();
        $document = Database::table("files")->where("document_key", $document_key)->first();
        $outputName = Str::random(32).".pdf";
        if (env('USE_CLOUD_CONVERT') == "Enabled" && !empty(env('CLOUDCONVERT_APP_KEY'))) {
            $api = new Api(env('CLOUDCONVERT_APP_KEY'));
            try {
                $api->convert([
                        'inputformat' => $document->extension,
                        'outputformat' => 'pdf',
                        'input' => 'upload',
                        'file' => fopen(config("app.storage").'/files/'.$document->filename, 'r'),
                    ])
                    ->wait()
                    ->download(config("app.storage").'/files/'.$outputName);
            } catch (\CloudConvert\Exceptions\ApiBadRequestException $e) {
                return responder("error", "Failed!", $e->getMessage());
            } catch (\CloudConvert\Exceptions\ApiConversionFailedException $e) {
                return responder("error", "Failed!", $e->getMessage());
            }  catch (\CloudConvert\Exceptions\ApiTemporaryUnavailableException $e) {
                return responder("error", "Failed!", $e->getMessage());
            } catch (\Exception $e) {
                return responder("error", "Failed!", $e->getMessage());
            }
        }else if(env('USE_CLOUD_CONVERT') == "Disabled"){    
            return responder("error", "Failed!", "Cloud Convert is not enabled, please enable on system settings page.");
        }else{    
            return responder("error", "Failed!", "Your Cloud Convert API Key is empty.");
        }
        self::deletefile($document->filename, true);
        self::keepcopy($outputName);
        $data = array(
                        "filename" => $outputName,
                        "size" => round(filesize(config("app.storage")."/files/".$outputName) / 1000),
                        "extension" => "pdf"
                    );
        Database::table("files")->where("document_key", $document_key)->update($data);
        $activity = 'File converted to PDF by <span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span>.';
        self::keephistory($document_key, $activity);
        return responder("success", "Complete!", "Conversion successfully completed.");
    }
    
    /**
     * Check file orientation
     * 
     * @param   float $width
     * @param   float $height
     * @return  string
     */
    public static function orientation($width, $height) {
        if ($width > $height) {
            return "L";
        }else{
            return "P";
        }
    }
    
    /**
     * Protect file
     * 
     * @param   string $document_key
     * @return  true
     */
    public static function protect($permission, $userpassword, $ownerpassword, $document_key) {
        $user = Auth::user();
        $document = Database::table("files")->where("document_key", $document_key)->first();
        $pdf = new PDF();
        $inputPath = config("app.storage")."/files/".$document->filename;
        $outputName = Str::random(32).".pdf";
        $outputPath = config("app.storage")."/files/". $outputName;
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if (env("PKI_STATUS") == "Enabled") {
            $certificate = 'file://'.realpath(config("app.storage").'/credentials/tcpdf.crt');
            $reason = $document->sign_reason.' • Digital Signature | '.$user->fname.' '.$user->lname.', '.self::ipaddress().','.date("F j, Y H:i");
            $info = array( 'Name' => $userName,  'Location' => env("APP_URL"), 'Reason' => $reason, 'ContactInfo' => env("APP_URL") );
            $pdf->setSignature($certificate, $certificate, 'information', '', 1, $info, true);
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetProtection($permission, $userpassword, $ownerpassword, 0, null);
        $pdf->numPages = $pdf->setSourceFile($inputPath);
        foreach(range(1, $pdf->numPages, 1) as $page) {
            try {
              $pdf->_tplIdx = $pdf->importPage($page);
            }
            catch(\Exception $e) {
              return false;
            }
            $size = $pdf->getTemplateSize($pdf->_tplIdx);
            $pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h']), true);
            $pdf->useTemplate($pdf->_tplIdx);
        }
        $pdf->Output($outputPath, 'F');
        Database::table("files")->where("document_key", $document_key)->update(array("filename" => $outputName));
        $activity = '<span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span> has activated document protection.'; 
        self::keephistory($document_key, $activity, "danger");
        self::deletefile($document->filename, true);
        self::keepcopy($outputName);
        return true;
    }
    
    /**
     * Sign & Edit document
     * 
     * @param   string $document_key
     * @return  array
     */
    public static function sign($document_key, $actions, $docWidth, $signing_key, $public = false) {
        if (!empty($signing_key)) {
            $request = Database::table("requests")->where("signing_key", $signing_key)->first();
            $sender = Database::table("users")->where("id", $request->sender)->first();
            $userName = $request->email;
            $user = Auth::user();
            $userName = $user->fname.' '.$user->lname;
            $signature = config("app.storage")."signatures/".$user->signature;
        }else if ($public) {
            $userName = "Guest";
            $signature = null;
        }else{
            $user = Auth::user();
            $userName = $user->fname.' '.$user->lname;
            $signature = config("app.storage")."signatures/".$user->signature;
        }
        
        $document = Database::table("files")->where("document_key", $document_key)->first();
        $pdf = new PDF(null, 'px');
        $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
        $inputPath = config("app.storage")."files/".$document->filename;
        $outputName = Str::random(32).".pdf";
        $outputPath = config("app.storage")."/files/". $outputName;
        $pdf->numPages = $pdf->setSourceFile($inputPath);
        $actions = json_decode($actions, true);
        $templateFields = array($docWidth);
        $signed = $updatedFields = $editted = false;
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if (env("PKI_STATUS") == "Enabled") {
            $certificate = 'file://'.realpath(config("app.storage").'/credentials/tcpdf.crt');
            $reason = $document->sign_reason.' • Digital Signature | '.$userName.', '.self::ipaddress().','.date("F j, Y H:i");
            $info = array( 'Name' => $userName,  'Location' => env("APP_URL"), 'Reason' => $reason, 'ContactInfo' => env("APP_URL") );
            $pdf->setSignature($certificate, $certificate, 'information', '', 1, $info, true);
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        foreach(range(1, $pdf->numPages, 1) as $page) {
            $rotate = false;
            $degree = 0;
            try {
              $pdf->_tplIdx = $pdf->importPage($page);
            }
            catch(\Exception $e) {
              return false;
            }
            foreach($actions as $action) {
                if(((int) $action['page']) === $page && $action['type'] == "rotate") {
                    $rotate = $editted = true;
                    $degree = $action['degree'];
                    break;
                }
            }
            $size = $pdf->getTemplateSize($pdf->_tplIdx);
            $scale = round($size['w'] / $docWidth, 3);
            $pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate'=>$degree), true);
            $pdf->useTemplate($pdf->_tplIdx);
            foreach($actions as $action) {
                if(((int) $action['page']) === $page) {
                    if ($action['group'] == "input") {
                        $updatedFields = true;
                        $templateFields[] = $action;
                        continue;
                    }elseif ($action['type'] == "image") {
                        $editted = true;
                        $imageArray = explode( ',', $action['image'] );
                        $imgdata = base64_decode($imageArray[1]);
                        $pdf->Image('@'.$imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($action['width'], $scale), self::scale($action['height'], $scale), '', '', '', false);
                    }elseif ($action['type'] == "symbol" || $action['type'] == "shape") {
                        $editted = true;
                        $content = str_replace("%22", '"', $action['image']);
                        $svg = File::write("system.svg", $content);
                        $pdf->ImageSVG($svg, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($action['width'], $scale), self::scale($action['height'], $scale), '', '', '', 0, false);
                    }else if ($action['type'] == "drawing") {
                        $editted = true;
                        $imageArray = explode( ',', $action['drawing'] );
                        $imgdata = base64_decode($imageArray[1]);
                        $pdf->Image('@'.$imgdata, 0, 0, $size['w'], $size['h'], '', '', '', false);
                    }else if ($action['type'] == "signature") {
                        $signed = true;
                        if (!$public) {
                            $pdf->Image($signature, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($action['width'], $scale), self::scale($action['height'], $scale), '', '', '', false);
                        }else{
                            $imageArray = explode( ',', $action['image'] );
                            $imgdata = base64_decode($imageArray[1]);
                            $pdf->Image('@'.$imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($action['width'], $scale), self::scale($action['height'], $scale), '', '', '', false);
                        }
                    }elseif ($action['type'] == "text") {
                        $editted = true;
                        $pdf->SetFont($action['font'], $action['bold'].$action['italic'], $action['fontsize'] - 1);
                        $pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $action['text']), 0, 0, false, true, '', true );
                    }
                }
            }
        }
        $pdf->Output($outputPath, 'F');
        if (count($templateFields) > 1) {
            Database::table("files")->where("document_key", $document_key)->update(array("filename" => $outputName, "editted" => "Yes", "template_fields" => json_encode($templateFields)));
        }else{
            Database::table("files")->where("document_key", $document_key)->update(array("filename" => $outputName, "editted" => "Yes"));
        }
        if (!empty($signing_key)) {
            $request = Database::table("requests")->where("signing_key", $signing_key)->first();
            $sender = Database::table("users")->where("id", $request->sender)->first();
            Database::table("requests")->where("signing_key", $signing_key)->update(array("status" => "Signed"));
            $notification = '<span class="text-primary">'.escape($userName).'</span> accepted a signing invitation of this <a href="'.url("Document@open").$request->document.'">document</a>.';
            Signer::notification($sender->id, $notification, "accept");
            $documentLink = env("APP_URL")."/document/".$request->document;
            $send = Mail::send(
                $sender->email, "Signing invitation accepted by ".$userName,
                array(
                    "title" => "Signing invitation accepted.",
                    "subtitle" => "Click the link below to view document.",
                    "buttonText" => "View Document",
                    "buttonLink" => $documentLink,
                    "message" => $userName." has accepted and signed the signing invitation you had sent. Click the link above to view the document.<br><br>Cheers!<br>".env("APP_NAME")." Team"
                ),
                "withbutton"
            );
        }
        if ($updatedFields) { 
            $activity = '<span class="text-primary">'.escape($userName).'</span> updated template fields document.'; 
            self::keephistory($document_key, $activity, "default");
        }
        if ($editted) {
            $activity = '<span class="text-primary">'.escape($userName).'</span> editted the document.'; 
            self::keephistory($document_key, $activity);
        }
        if ($signed) { 
            Database::table("files")->where("document_key", $document_key)->update(array("status" => "Signed"));
            $activity = '<span class="text-primary">'.escape($userName).'</span> signed the document.'; 
            self::keephistory($document_key, $activity, "success");
        }
        self::deletefile($document->filename, "original");
        self::renamecopy($document->filename, $outputName);
        return true;
    }
    
    /**
     * Scale element dimension
     * 
     * @param   int $dimension
     * @return  int
     */
    public static function scale($dimension, $scale) {
        return round($dimension * $scale);
    }
    
    /**
     * Scale position on axis
     * 
     * @param   int $position
     * @return  int
     */
    public static function adjustPositions($position) {
        return round($position - 83);
    }
    
    /**
     * Get Ip Address
     * 
     * @param   int $position
     * @return  int
     */
    public static function ipaddress() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
     
        return $ipaddress;
    }

}