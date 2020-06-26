<?php
namespace Simcify\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Simcify\Auth;

class Authenticate implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http]Request
     */
    public function handle(Request $request) {

        Auth::remember();
        
        if (Auth::check()) {
            $request->user = Auth::user();
            // Set the locale to the user's preference
            config('app.locale.default', $request->user->{config('auth.locale')});
        } else {
            if (isset($_GET['signingKey'])) { 
                $guest = serialize(array(self::getDocumentKey(), $_GET['signingKey']));
                cookie("guest", $guest, 7);
            }
            $request->setRewriteUrl(url('Auth@get'));
        }
        return $request;

    }
    
    /**
     * Get document key from signing url
     * 
     * @return  $document_key
     */
    public static function getDocumentKey() {
        $fullUrl = $_SERVER['REQUEST_URI'];
        $signingKey = $_GET['signingKey'];
        $basename = basename($fullUrl);
        $unwantedPart = "?signingKey=".$signingKey;
        $document_key = str_replace($unwantedPart, "", $basename);
        return $document_key;
    }
}
