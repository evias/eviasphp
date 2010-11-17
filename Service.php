<?php

class eVias_Service
    extends eVias_Service_Abstract
{
    static private $_instances = null;
    static private $_classByKey = array(
        'catalogue'     => 'eVias_Service_Catalogue',
        'member'        => 'eVias_Service_Member',
        'blog'          => 'eVias_Service_Blog'
    );

    /**
     * @method factory(string)
     *
     * @brief
     *   - instante an eVias_Service_* class object
     *
     * @param   $serviceKeyName     string  key representing the service (@see $_classByKey)
     *
     * @return eVias_Service_Abstract
     * @throws eVias_Service_Exception
     */
    static public factory($serviceKeyName)
    {
        if (! in_array($serviceKeyName, array_keys(self::$_classByKey))) {
            throw new eVias_Service_Exception ("The service with key $serviceKeyName is not registered in eVias_Service::factory");
        }

        if (is_null(self::$_instances)) {
            self::$_instances = array();
        }

        if (empty(self::$_instances[$serviceKeyName])) {
            // no instance for given serviceKeyName

            $serviceClass = self::$_classByKey[$serviceKeyName];

            self::$_instances[$serviceKeyName] = new $serviceClass;
        }


        return self::$_instances[$serviceKeyName];
    }

}

