<?php


namespace AppBundle\Helpers;


use AppBundle\Security\ACLSecurityProvider;
use Symfony\Component\HttpFoundation\RequestStack;

class ArrayParser
{
    
    /**
     * @param $array
     * @param $key
     * @return null
     */
    public function getFieldValue($array,$key)
    {
        if(isset($array[$key]))
            return $array[$key];

        return null;
    }

    
}