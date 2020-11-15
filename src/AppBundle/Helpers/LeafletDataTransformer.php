<?php

namespace AppBundle\Helpers;


class LeafletDataTransformer
{
    
    public function formatArrayToString($data)
    {
        $str = '';

        foreach ($data as $row)
        {
            foreach ($row as $i => $attr)
            {
                $str.= $attr.", ";
            }
            
            $str.= ";";
        }
        
       return $str;
    }

}