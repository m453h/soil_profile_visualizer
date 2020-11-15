<?php

namespace AppBundle\Helpers;

use \Symfony\Component\Yaml\Yaml;

class FileLoader
{

    public function loadFile($fileName)
    {

        try
        {
            $values = Yaml::parse(file_get_contents($fileName));

            return $values;
        }  
        catch (\Exception $e)
        {
            return null;
        }
    }
    
    public function parsePermissionsFile()
    {
        
    }
}