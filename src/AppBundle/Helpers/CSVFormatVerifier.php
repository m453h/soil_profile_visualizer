<?php


namespace AppBundle\Helpers;


class CSVFormatVerifier
{
    
    public function verifyFileFormatHeaders($expectedHeaders,$availableHeaders)
    {
        foreach ($expectedHeaders as $expectedHeader)
        {
            if(!in_array($expectedHeader,$availableHeaders))
            {
                return false;
            }
            
        }

        return true;
    }

}