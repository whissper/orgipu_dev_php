<?php

namespace ws;

use PDO;
use dbcengine\QueryEngine;
use dbcengine\BoundParameter;

/**
 * Consumption class
 */
class Consumption {

    private $queryEngine;
    
    //constructor
    function __construct(QueryEngine $queryEngine) {
        $this->queryEngine = $queryEngine;
    }

    /**
      Method: select metering value from DB
      @param $device_id_val - device id
      @param $calc_month_val - month
      @param $calc_year_val - year
     */
    private function selectCalcValue($device_id_val, $calc_month_val, $calc_year_val) {
        $returnVal = array('calc_value' => 0.000, 'is_normative' => 0);

        $id_device = intval($device_id_val);
        $calc_month = intval($calc_month_val);
        $calc_year = intval($calc_year_val);

        if ($calc_month <= 0) {
            $calc_year = $calc_year - 1 - ( abs($calc_month) - abs($calc_month) % 12 ) / 12; // = year - 1 - ( |month| - |month| % 12 )/12
            $calc_month = 12 - ( abs($calc_month) % 12 ); // = 12 - ( |month| % 12 )		
        }

        $params = array();
        $params[] = new BoundParameter(':device_id', $id_device, PDO::PARAM_INT);
        $params[] = new BoundParameter(':calc_month', $calc_month, PDO::PARAM_INT);
        $params[] = new BoundParameter(':calc_year', $calc_year, PDO::PARAM_INT);

        $query_string_consume = 'SELECT `calc_value`, `is_normative` 
                                 FROM `metering_values` 
                                 WHERE `device_id` = :device_id AND 
                                       `calc_month` = :calc_month AND 
                                       `calc_year` = :calc_year';

        $resultSet = $this->queryEngine->getResultSet($query_string_consume, $params);

        $rows = $resultSet->fetchAll();
        $num_rows = count($rows);

        if ($num_rows > 0) {//values exist
            foreach ($rows as $row) {
                $returnVal['calc_value'] = $row['calc_value'];
                $returnVal['is_normative'] = $row['is_normative'];
            }
            return $returnVal;
        } else {//values don't exist
            return null;
        }
    }

    /**
      Method: check if Normative could be in range (10 month backward)
      @param $device_id_val - device id
      @param $calc_month_val - month
      @param $calc_year_val - year
     */
    private function normativeInRange($device_id_val, $calc_month_val, $calc_year_val) {
        $returnVal = array('inRange' => false, 'normativeDate' => '', 'monthsAmount' => 6, 'countAsNormative' => false);

        $inStack = 0;

        for ($i = 1; $i <= 10; $i++) {
            $selectedCalcValue = $this->selectCalcValue($device_id_val, $calc_month_val - $i, $calc_year_val);

            if (!is_null($selectedCalcValue)) {
                $inStack = 0;

                if ($selectedCalcValue['is_normative'] == 1) {
                    if ($i > 3) {
                        $returnVal['inRange'] = false;
                        $returnVal['monthsAmount'] = $i - 1;
                        $returnVal['countAsNormative'] = false;
                        return $returnVal;
                        break;
                    } else {
                        $returnVal['inRange'] = false;
                        $returnVal['monthsAmount'] = $i - 1;
                        $returnVal['countAsNormative'] = true;
                        return $returnVal;
                        break;
                    }
                }
            } else {
                if ($inStack == 0) {
                    if (($calc_month_val - $i) <= 0) {
                        $revertMonth = abs($calc_month_val - $i);
                        $revertYear = $calc_year_val - 1;

                        $revertYear = $revertYear - ( $revertMonth - $revertMonth % 12 ) / 12;
                        $revertMonth = 12 - ( $revertMonth % 12 );
                        $returnVal['normativeDate'] = ($revertMonth >= 10 ? $revertMonth : '0' . $revertMonth) . '.' . $revertYear;
                    } else {
                        $returnVal['normativeDate'] = (($calc_month_val - $i) >= 10 ? ($calc_month_val - $i) : '0' . ($calc_month_val - $i)) . '.' . $calc_year_val;
                    }
                }

                $inStack++;
            }

            if ($inStack == 4) {
                $returnVal['inRange'] = true;
                return $returnVal;
                break;
            }

            if ($i == 10) {
                $returnVal['inRange'] = false;
                return $returnVal;
            }
        }
    }

    /**
      Method: calculate average consume data
      @param $device_id_val - device id
      @param $calc_month_val - month
      @param $calc_year_val - year
     */
    private function calcAverageConsume($device_id_val, $calc_month_val, $calc_year_val) {
        $meteringVal = 0.000;
        $consumptionSum = 0.000;

        $returnVal = array('isPossible' => true, 'countAsNormative' => false, 'normativeDate' => '', 'averageConsumeVal' => 0.000, 'lastMeteringVal' => 0.000);

        $previousExists = false;

        $normativeInRangeArray = $this->normativeInRange($device_id_val, $calc_month_val, $calc_year_val);

        if (!$normativeInRangeArray['inRange']) {
            if (!$normativeInRangeArray['countAsNormative']) {
                for ($i = 1; $i <= ($normativeInRangeArray['monthsAmount'] + 1); $i++) {
                    $selectedCalcValue = $this->selectCalcValue($device_id_val, $calc_month_val - $i, $calc_year_val);

                    if (!is_null($selectedCalcValue)) {
                        if ($i == 1) {
                            $previousExists = true;
                            $meteringVal = $selectedCalcValue['calc_value'];
                            $returnVal['lastMeteringVal'] = $meteringVal;
                        } else if ($i == ($normativeInRangeArray['monthsAmount'] + 1)) {
                            if ($previousExists) {
                                $consumptionSum += $returnVal['lastMeteringVal'] - ($selectedCalcValue['calc_value'] + $consumptionSum);
                            } else {
                                $returnVal['averageConsumeVal'] = 0.000;
                            }

                            $meteringVal = $selectedCalcValue['calc_value'];
                        } else {
                            if ($previousExists) {
                                $consumptionSum += $returnVal['lastMeteringVal'] - ($selectedCalcValue['calc_value'] + $consumptionSum);
                            } else {
                                $consumptionSum += ( $meteringVal - $selectedCalcValue['calc_value'] ) + $returnVal['averageConsumeVal'];
                                $returnVal['averageConsumeVal'] = 0.000;
                            }

                            $meteringVal = $selectedCalcValue['calc_value'];
                        }
                    } else {
                        $averageConsumption = $this->calcAverageConsume($device_id_val, $calc_month_val - $i, $calc_year_val);

                        if (!$averageConsumption['isPossible']) {
                            $consumptionSum = null;
                            break;
                        }

                        if ($i == ($normativeInRangeArray['monthsAmount'] + 1) && !$previousExists) {
                            //do nothing
                        } else {
                            $consumptionSum += $averageConsumption['averageConsumeVal'];
                        }

                        $meteringVal = $averageConsumption['lastMeteringVal'];

                        if ($i == $normativeInRangeArray['monthsAmount']) {
                            $returnVal['averageConsumeVal'] = 0.000;
                        } else {
                            $returnVal['averageConsumeVal'] = $averageConsumption['averageConsumeVal'];
                        }
                    }
                }

                if (is_null($consumptionSum)) {
                    $returnVal['isPossible'] = $averageConsumption['isPossible'];
                    $returnVal['countAsNormative'] = $averageConsumption['countAsNormative'];
                    $returnVal['normativeDate'] = $averageConsumption['normativeDate'];
                    return $returnVal;
                } else {
                    $returnVal['isPossible'] = true;
                    $returnVal['countAsNormative'] = false;
                    $returnVal['averageConsumeVal'] = $consumptionSum / $normativeInRangeArray['monthsAmount'];
                    return $returnVal;
                }
            } else {
                $returnVal['isPossible'] = false;
                $returnVal['countAsNormative'] = true;
                return $returnVal;
            }
        } else {
            $returnVal['isPossible'] = false;
            $returnVal['countAsNormative'] = false;
            $returnVal['normativeDate'] = $normativeInRangeArray['normativeDate'];
            return $returnVal;
        }
    }

    /**
      Method: get consumption value
      @param $device_id_val - device id
      @param $calc_month_val - month
      @param $calc_year_val - year
     */
    public function getConsumptionValue($device_id_val, $calc_month_val, $calc_year_val) {
        $current_val = null;
        $previous_val = null;

        $month = $calc_month_val;

        $current_val = $this->selectCalcValue($device_id_val, $month, $calc_year_val);

        if (!is_null($current_val)) {//current month(period) values exist
            $month = $month - 1;
            $previous_val = $this->selectCalcValue($device_id_val, $month, $calc_year_val);

            if (!is_null($previous_val)) {//previous month(period) values exist
                $consumption_val = $current_val['calc_value'] - $previous_val['calc_value'];
                return round($consumption_val, 3);
            } else {//there are no metering values for previuos month(period)
                for ($i = 1; $i <= 3; $i++) {
                    $selectedCalcValue = $this->selectCalcValue($device_id_val, $month - $i, $calc_year_val);

                    if (!is_null($selectedCalcValue) && $i <= 3) {
                        $averageConsumeValSum = 0.000;

                        for ($j = $i - 1; $j >= 0; $j--) {
                            $averageConsume = $this->calcAverageConsume($device_id_val, $month - $j, $calc_year_val);

                            if ($averageConsume['isPossible']) {
                                $averageConsumeValSum += $averageConsume['averageConsumeVal'];
                            } else {
                                $averageConsumeValSum = null;
                                break;
                            }
                        }

                        if (is_null($averageConsumeValSum)) {
                            if ($averageConsume['countAsNormative']) {
                                //return 'NORMATIVE|На основе анализа возможности расчета среднего значения';
                                return 'ERROR|Отсутствие показаний по нормативу за предыдущий период.';
                            } else {
                                return 'ERROR|Отсутствие показаний по нормативу за период ' . $averageConsume['normativeDate'];
                            }
                        } else {
                            $consumption_val = $current_val['calc_value'] - $selectedCalcValue['calc_value'] - $averageConsumeValSum;
                            return round($consumption_val, 3);
                        }
                        break;
                    } else if (is_null($selectedCalcValue) && $i == 3) {
                        //four previous months without values
                        return 'ERROR|Предыдущие 4 месяца без показаний';
                    }
                }
            }
        } else {//there are no metering values for current month(period)
            for ($i = 1; $i <= 3; $i++) {
                $selectedCalcValue = $this->selectCalcValue($device_id_val, $month - $i, $calc_year_val);

                if (!is_null($selectedCalcValue) && $i <= 3) {
                    $averageConsume = $this->calcAverageConsume($device_id_val, $month, $calc_year_val);

                    if ($averageConsume['isPossible']) {
                        return round($averageConsume['averageConsumeVal'], 3);
                    } else {
                        if ($averageConsume['countAsNormative']) {
                            return 'NORMATIVE|На основе анализа возможности расчета среднего значения';
                        } else {
                            return 'ERROR|Отсутствие показаний по нормативу за период ' . $averageConsume['normativeDate'];
                        }
                    }
                    break;
                } else if (is_null($selectedCalcValue) && $i == 3) {
                    //three previous months without values
                    return 'NORMATIVE|Предыдущие три месяца без показаний';
                }
            }
        }
    }

}
