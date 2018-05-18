<?php

namespace ws;

use SoapClient;

/**
 * ConsumptionSOAP class
 */
class ConsumptionSOAP {
    
    /**
     * get formed XLSX document
     * @param type $postData
     * @return type String (reference to file)
     */
    public static function writeDataIntoXLSX($postData) {
        try {
            $client = new SoapClient("http://kom-ts01-dev01:8080/Consumption/ConsumptionWS?wsdl");

            if (!isset($postData['month'])) {
                $month = date('m');
            } else {
                $month = $postData['month'];
            }

            if (!isset($postData['year'])) {
                $year = date('Y');
            } else {
                $year = $postData['year'];
            }

            $params = array('month' => $month, 'year' => $year);

            $result = $client->loadXLSX($params);

            return $result->{'reference'};
        } catch (Exception $ex) {
            return 'ERROR_WS|' . $ex->getMessage();
        }
    }

}
