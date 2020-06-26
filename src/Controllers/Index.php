<?php
namespace Simcify\Controllers;

class Index{

    /**
     * Get a sample view or redirect to it
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        return view('index');
    }

    /**
     * Get a 404 view
     * 
     * @return \Pecee\Http\Response
     */
    public function error404() {
        return view('errors/404');
    }

}
