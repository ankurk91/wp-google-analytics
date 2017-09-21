<?php

namespace Ankur\Plugins\Ank_Simplified_GA;

/**
 * Class Singleton
 * @package Ankur\Plugins\Ank_Simplified_GA
 */
class Singleton
{
    protected static $instances = array();

    private function __construct()
    {
        // Singleton cannot be directly constructed, so we make this protected
    }

    /**
     * Function to instantiate our class and make it a singleton
     * @return mixed
     */
    public static function instance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static;
        }
        return self::$instances[$class];
    }

    protected function __clone()
    {
        // Don't not allow clones
    }

    public function __wakeup()
    {
        return new \Exception("Cannot unserialize singleton");
    }

}
