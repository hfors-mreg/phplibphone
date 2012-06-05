<?php
/**
 * This file is part of the phplibphone package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 *
 * @package phplibphone
 *
 * @subpackage Library
 */
namespace itbz\phplibphone\Library;
use itbz\phplibphone\Exception;
use SimpleXMLElement;


/**
 * Fetch carrier information for swedish phone numbers from api.pts.se
 *
 * @package phplibphone
 *
 * @subpackage Library
 */
class PhoneCarrier implements \itbz\phplibphone\CarrierLookupInterface
{

    /**
     * Get country code this library handles
     *
     * @return int
     */
    public function getCountryCode()
    {
        return 46;
    }


    /**
     * Fetch carrier information from api.pts.se
     *
     * @param string $ndc National destination code
     *
     * @param string $sn Subscriber number
     *
     * @return string Name of carrier, empty string if nothing could be fetched
     *
     * @throws Exception if unable to reach api.pts.se, or XML is broken
     */
    public function lookup($ndc, $sn);
        $url = "http://api.pts.se/ptsnumber/ptsnumber.asmx/SearchByNumber";
        $query = sprintf('?Ndc=%sNumber=%s', urlencode($ndc), urlencode($sn));
        $page = @file_get_contents($url . $query);
        
        if ($page) {
            throw new Exception("Unable to fetch carrier from $url");
        }

        libxml_use_internal_errors(TRUE);
        $xml = new SimpleXMLElement($page);

        if (!$xml instanceof SimpleXMLElement || !isset($xml->Operator)) {
            throw new Exception("Invalid XML returned from $url");
        }

        if (
            $xml->Operator=='Ogiltigt värde'
            || $xml->Operator=='Finns ingen operatör med detta nummer'
        ) {
            return '';
        }
        
        return (string)$xml->Operator;
    }

}