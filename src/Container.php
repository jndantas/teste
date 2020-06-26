<?php
namespace Simcify;

final class Container {
    /**
     * Hold the class instance
     * 
     * @var \DI\Container
     */
    private static $__instance;
    
    /**
     * Create a new container object
     * 
     * @return  void
     */
    private function __construct() {
        // Expensive stuff
    }
   
    /**
     * Get the instance of the container
     * 
     * @return  \DI\Container
     */
    public static function getInstance() {
        return static::$__instance;
    }
   
    /**
     * Set the instance of the container
     * 
     * @param  \DI\Container    $container
     * @return  void
     */
    public static function setInstance($container) {
        static::$__instance = $container;
    }
}
