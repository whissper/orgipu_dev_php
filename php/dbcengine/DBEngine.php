<?php

namespace dbcengine;

use PDO;
use PDOException;
use utils\Utils;
use ws\Consumption;

/**
 * DBEngine class
 */
class DBEngine {

    private $pdo;
    
    //constructor
    function __construct() {
        
    }
    
    //create PDO object
    private function createPDO() {
        $dsn = 'mysql:host=' . DBconf::DB_SERVER . ';dbname=' . DBconf::DB_NAME . ';charset=' . DBconf::CHARSET;

        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new PDO($dsn, DBconf::DB_USER, DBconf::DB_PASS, $opt);
    }
    
    //destroy PDO object
    private function destroyPDO() {
        $this->pdo = null;
    }
    
    /**
     * select Login data
     * @param type $login
     * @param type $password
     * @return JSON string
     */
    public function selectLoginData($login, $password) {
        $params = array();
        $params[] = new BoundParameter(':login', $login, PDO::PARAM_STR);
        $params[] = new BoundParameter(':password', $password, PDO::PARAM_STR);

        $rows = array();
        $rows['error'] = '';
        $rows['id'] = '';
        $rows['role'] = '';

        try {
            $this->createPDO();

            $queryEngine = new QueryEngine($this->pdo);

            $queryString = 'SELECT * FROM `users` WHERE `users`.`login` = :login AND `users`.`pass` = :password ORDER BY `users`.`id` DESC';

            $resultSet = $queryEngine->getResultSet($queryString, $params);

            while ($row = $resultSet->fetch(PDO::FETCH_LAZY)) {
                $rows['id'] = $row->id;
                $rows['role'] = $row->role;
            }
        } catch (PDOException $e) {
            $rows['error'] = 'ERROR_PDO|' . $e->getMessage();
        }

        $this->destroyPDO();

        return $rows;
    }
    
    /**
     * SELECT some data
     * @param type $queryName
     * @param type $postData
     * @return JSON string
     */
    public function selectData($queryName, $postData) {
        $resultString = '';

        $queryStringCount = '';
        $queryString = '';
        $params = array();
        $dataColumns = array();
        $count_rows = '0';

        switch ($queryName) {
            case 'select_contracts':
                $queryStringCount = 'SELECT COUNT(`contract`.`id`) AS "countrows" 
                                     FROM `contract` 
                                     WHERE `contract`.`id` REGEXP :contract_id AND 
                                           `contract`.`contract_num` REGEXP :contract_num';

                $queryString = 'SELECT `contract`.`id`, `contract`.`contract_num` 
                                FROM `contract`  
                                WHERE `contract`.`id` REGEXP :contract_id AND 
                                      `contract`.`contract_num` REGEXP :contract_num
                                ORDER BY `contract`.`contract_num` ASC
                                LIMIT ' . $postData['start_position'] . ', ' . $postData['per_page'];

                $params[] = new BoundParameter(':contract_id', $postData['contract_id'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':contract_num', $postData['contract_num'], PDO::PARAM_STR);

                $dataColumns[] = 'id';
                $dataColumns[] = 'contract_num';
                break;
            case 'select_heated_objects':
                $queryStringCount = 'SELECT COUNT(`heated_object`.`id`) AS "countrows" 
                                     FROM `heated_object`
                                     LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                                     WHERE `heated_object`.`id` REGEXP :heated_object_id AND 
                                           `heated_object`.`name` REGEXP :heated_object_name AND 
                                           `contract`.`contract_num` REGEXP :contract_num';

                $queryString = 'SELECT `heated_object`.`id`, `heated_object`.`name`, `contract`.`contract_num` 
                                FROM `heated_object`
                                LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                                WHERE `heated_object`.`id` REGEXP :heated_object_id AND 
                                      `heated_object`.`name` REGEXP :heated_object_name AND 
                                      `contract`.`contract_num` REGEXP :contract_num
                                ORDER BY `heated_object`.`name` ASC
                                LIMIT ' . $postData['start_position'] . ', ' . $postData['per_page'];

                $params[] = new BoundParameter(':heated_object_id', $postData['heated_object_id'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':heated_object_name', $postData['heated_object_name'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':contract_num', $postData['contract_num'], PDO::PARAM_STR);

                $dataColumns[] = 'id';
                $dataColumns[] = 'name';
                $dataColumns[] = 'contract_num';
                break;
            case 'select_devices':
                $queryStringCount = 'SELECT COUNT(`device`.`id`) AS "countrows" 
                                     FROM `device`
                                     LEFT JOIN `heated_object` ON `heated_object`.`id` = `device`.`heated_object_id` 
                                     LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                                     WHERE `device`.`id` REGEXP :device_id AND 
                                           `device`.`device_num` REGEXP :device_num AND 
                                           `heated_object`.`name` REGEXP :heated_object_name AND 
                                           `device`.`heated_object_id` REGEXP :heated_object_id AND 
                                           `contract`.`contract_num` REGEXP :contract_num AND
										   `device`.`is_boiler` REGEXP :is_boiler AND
										   `device`.`is_heatmeter` REGEXP :is_heatmeter';

                $queryString = 'SELECT `device`.`id`, `device`.`device_num`, `heated_object`.`name`, 
                                       `device`.`heated_object_id`, `contract`.`contract_num`, `device`.`is_boiler`, `device`.`is_heatmeter`   
                                FROM `device`
                                LEFT JOIN `heated_object` ON `heated_object`.`id` = `device`.`heated_object_id` 
                                LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                                WHERE `device`.`id` REGEXP :device_id AND 
                                      `device`.`device_num` REGEXP :device_num AND 
                                      `heated_object`.`name` REGEXP :heated_object_name AND 
                                      `device`.`heated_object_id` REGEXP :heated_object_id AND 
                                      `contract`.`contract_num` REGEXP :contract_num AND 
									  `device`.`is_boiler` REGEXP :is_boiler AND
									  `device`.`is_heatmeter` REGEXP :is_heatmeter
                                ORDER BY `device`.`id` ASC 
                                LIMIT ' . $postData['start_position'] . ', ' . $postData['per_page'];

                $params[] = new BoundParameter(':device_id', $postData['device_id'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':device_num', $postData['device_num'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':heated_object_name', $postData['heated_object_name'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':heated_object_id', $postData['heated_object_id'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':contract_num', $postData['contract_num'], PDO::PARAM_STR);
				$params[] = new BoundParameter(':is_boiler', $postData['is_boiler'], PDO::PARAM_STR);
				$params[] = new BoundParameter(':is_heatmeter', $postData['is_heatmeter'], PDO::PARAM_STR);

                $dataColumns[] = 'id';
                $dataColumns[] = 'device_num';
                $dataColumns[] = 'heated_object_id';
                $dataColumns[] = 'name';
                $dataColumns[] = 'contract_num';
                $dataColumns[] = 'is_boiler';
				$dataColumns[] = 'is_heatmeter';
                break;
            case 'select_devicevals':
                $queryStringCount = 'SELECT COUNT(`metering_values`.`id`) AS "countrows" 
                                     FROM `metering_values` 
                                     LEFT JOIN `device` ON `device`.`id` = `metering_values`.`device_id` 
                                     LEFT JOIN `heated_object` ON `heated_object`.`id` = `device`.`heated_object_id` 
                                     LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                                     WHERE `metering_values`.`id` REGEXP :metering_values_id AND 
                                            `metering_values`.`device_id` REGEXP :device_id AND 
                                            `device`.`device_num` REGEXP :device_num AND 
                                            `metering_values`.`calc_month` REGEXP :calc_month AND 
                                            `metering_values`.`calc_year` REGEXP :calc_year AND 
                                            `heated_object`.`name` REGEXP :heated_object_name AND 
                                            `contract`.`contract_num` REGEXP :contract_num';

                $queryString = 'SELECT `metering_values`.`id`, `metering_values`.`device_id`, 
                                       `device`.`device_num`, `metering_values`.`calc_value`, 
                                       `metering_values`.`calc_month`, `metering_values`.`calc_year`, 
                                       `heated_object`.`id` AS "heated_object_id", 
                                       `heated_object`.`name` AS "heated_object_name", 
                                       `contract`.`contract_num`, 
                                       `metering_values`.`is_normative` 
                                FROM `metering_values` 
                                LEFT JOIN `device` ON `device`.`id` = `metering_values`.`device_id` 
                                LEFT JOIN `heated_object` ON `heated_object`.`id` = `device`.`heated_object_id` 
                                LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                                WHERE `metering_values`.`id` REGEXP :metering_values_id AND 
                                      `metering_values`.`device_id` REGEXP :device_id AND 
                                      `device`.`device_num` REGEXP :device_num AND 
                                      `metering_values`.`calc_month` REGEXP :calc_month AND 
                                      `metering_values`.`calc_year` REGEXP :calc_year AND 
                                      `heated_object`.`name` REGEXP :heated_object_name AND 
                                      `contract`.`contract_num` REGEXP :contract_num 
                                ORDER BY `device`.`device_num` ASC, `metering_values`.`calc_year` DESC, `metering_values`.`calc_month` DESC 
                                LIMIT ' . $postData['start_position'] . ', ' . $postData['per_page'];

                $params[] = new BoundParameter(':metering_values_id', $postData['metering_values_id'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':device_id', $postData['device_id'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':device_num', $postData['device_num'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':calc_month', $postData['calc_month'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':calc_year', $postData['calc_year'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':heated_object_name', $postData['heated_object_name'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':contract_num', $postData['contract_num'], PDO::PARAM_STR);

                $dataColumns[] = 'id';
                $dataColumns[] = 'contract_num';
                $dataColumns[] = 'heated_object_id';
                $dataColumns[] = 'heated_object_name';
                $dataColumns[] = 'device_id';
                $dataColumns[] = 'device_num';
                $dataColumns[] = 'calc_value';
                $dataColumns[] = 'calc_month';
                $dataColumns[] = 'calc_year';
                $dataColumns[] = 'is_normative';
                break;
        }

        try {
            $this->createPDO();
            $queryEngine = new QueryEngine($this->pdo);
            $resultSet = $queryEngine->getResultSet($queryStringCount, $params);
            while ($row = $resultSet->fetch(PDO::FETCH_LAZY)) {
                $count_rows = $row->countrows;
            }
            $resultString = '{ "countrows" : "' . $count_rows . '", ';
            $resultString .= '"page" : "' . $postData['page'] . '", ';
            $resultString .= '"perpage" : "' . $postData['per_page'] . '", ';
            $resultString .= '"rowitems" : [ ';

            $resultSet = $queryEngine->getResultSet($queryString, $params);
            while ($row = $resultSet->fetch(PDO::FETCH_LAZY)) {
                $resultString .= '["';

                $count = count($dataColumns);
                foreach ($dataColumns as $columnName) {
                    $count--;
                    if ($count != 0) {
                        $resultString .= Utils::json_string_encode($row[$columnName]) . '", "';
                    } else {
                        $resultString .= Utils::json_string_encode($row[$columnName]);
                    }
                }

                $resultString .= '"],';
            }

            $resultString .= '[] ] }';
        } catch (PDOException $e) {
            $resultString = 'ERROR_PDO|' . $e->getMessage();
        }
        $this->destroyPDO();
        return $resultString;
    }
    
    /**
     * SELECT Consumption
     * @param type $postData
     * @return JSON string
     */
    public function selectConsumption($postData) {
        $resultString = '';

        $queryStringCount = '';
        $queryString = '';
        $params = array();
        $count_rows = '0';

        $queryStringCount = 'SELECT COUNT(`device`.`id`) AS "countrows" 
                             FROM `device`
                             LEFT JOIN `heated_object` ON `heated_object`.`id` = `device`.`heated_object_id` 
                             LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                             WHERE `device`.`id` REGEXP :device_id AND 
                                   `device`.`device_num` REGEXP :device_num AND 
                                   `heated_object`.`name` REGEXP :heated_object_name AND 
                                   `device`.`heated_object_id` REGEXP :heated_object_id AND 
                                   `contract`.`contract_num` REGEXP :contract_num';

        $queryString = 'SELECT `device`.`id`, `device`.`device_num`, `heated_object`.`name`, 
                               `device`.`heated_object_id`, `contract`.`contract_num`, `device`.`is_boiler`, `device`.`is_heatmeter`   
                        FROM `device`
                        LEFT JOIN `heated_object` ON `heated_object`.`id` = `device`.`heated_object_id` 
                        LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                        WHERE `device`.`id` REGEXP :device_id AND 
                              `device`.`device_num` REGEXP :device_num AND 
                              `heated_object`.`name` REGEXP :heated_object_name AND 
                              `device`.`heated_object_id` REGEXP :heated_object_id AND 
                              `contract`.`contract_num` REGEXP :contract_num 
                        ORDER BY `heated_object`.`name` ASC 
                        LIMIT ' . $postData['start_position'] . ', ' . $postData['per_page'];

        $params[] = new BoundParameter(':device_id', $postData['device_id'], PDO::PARAM_STR);
        $params[] = new BoundParameter(':device_num', $postData['device_num'], PDO::PARAM_STR);
        $params[] = new BoundParameter(':heated_object_name', $postData['heated_object_name'], PDO::PARAM_STR);
        $params[] = new BoundParameter(':heated_object_id', $postData['heated_object_id'], PDO::PARAM_STR);
        $params[] = new BoundParameter(':contract_num', $postData['contract_num'], PDO::PARAM_STR);

        try {
            $this->createPDO();
            $queryEngine = new QueryEngine($this->pdo);
            $resultSet = $queryEngine->getResultSet($queryStringCount, $params);
            while ($row = $resultSet->fetch(PDO::FETCH_LAZY)) {
                $count_rows = $row->countrows;
            }
            $resultString = '{ "countrows" : "' . $count_rows . '", ';
            $resultString .= '"page" : "' . $postData['page'] . '", ';
            $resultString .= '"perpage" : "' . $postData['per_page'] . '", ';
            $resultString .= '"rowitems" : [ ';

            $resultSet = $queryEngine->getResultSet($queryString, $params);

            $calcConsumption = new Consumption($queryEngine);

            while ($row = $resultSet->fetch(PDO::FETCH_LAZY)) {
                $col_consumption_val_m3 = $calcConsumption->getConsumptionValue($row['id'], $postData['calc_month'], $postData['calc_year']);

                if (strpos($col_consumption_val_m3, 'NORMATIVE') !== false || strpos($col_consumption_val_m3, 'ERROR') !== false) {
                    $col_consumption_val_gk = $col_consumption_val_m3;
                } else {
                    $col_consumption_val_gk = $col_consumption_val_m3 * 0.0713; //0.0713 -- special coefficient
                    $col_consumption_val_gk = round($col_consumption_val_gk, 3);
                }

                //if device "is boiler" then consumption_val_m3 = "BOILER"
                if (intval($row['is_boiler']) == 1) {
                    $col_consumption_val_m3 = 'BOILER';
                }
				
				//if device "is heatmeter" then col_consumption_val_gk = $col_consumption_val_m3 AND consumption_val_m3 = "HEATMETER"
                if (intval($row['is_heatmeter']) == 1) {
                    $col_consumption_val_gk = $col_consumption_val_m3;
					$col_consumption_val_m3 = 'HEATMETER';				
                }

                //if "hide normative values" checkbox is checked
                if ($postData['hide_normative_vals'] == 1 &&
                        ( strpos($col_consumption_val_m3, 'NORMATIVE') !== false ||
                        strpos($col_consumption_val_gk, 'NORMATIVE') !== false )
                ) {
                    //DO NOTHING...
                } else {
                    $resultString .= '["' . Utils::json_string_encode($row['contract_num']) . '", "' .
                            Utils::json_string_encode($row['heated_object_id']) . '", "' .
                            Utils::json_string_encode($row['name']) . '", "' .
                            Utils::json_string_encode($row['id']) . '", "' .
                            Utils::json_string_encode($row['device_num']) . '", "' .
                            Utils::json_string_encode($col_consumption_val_m3) . '", "' .
                            Utils::json_string_encode($col_consumption_val_gk) . '"], ';
                }
            }

            $resultString .= '[] ] }';
        } catch (PDOException $e) {
            $resultString = 'ERROR_PDO|' . $e->getMessage();
        }
        $this->destroyPDO();
        return $resultString;
    }
    
    /**
     * SELECT some data BY ID
     * @param type $queryName
     * @param type $id
     * @return JSON string
     */
    public function selectDataByID($queryName, $id) {
        $resultString = '';
        $queryString = '';
        $entityName = '';
        $propertyNames = array();
        $dataColumns = array();

        $params = array();
        $params[] = new BoundParameter(':id', $id, PDO::PARAM_INT);

        switch ($queryName) {
            case 'select_contract_by_id':
                $queryString = 'SELECT * FROM `contract` 
				WHERE `id` = :id';

                $entityName = 'contract';

                $propertyNames[] = 'idUpd';
                $propertyNames[] = 'numContractUpd';

                $dataColumns[] = 'id';
                $dataColumns[] = 'contract_num';
                break;
            case 'select_heated_object_by_id':
                $queryString = 'SELECT `heated_object`.`id`, `heated_object`.`name`, `contract`.`contract_num` 
                                FROM `heated_object`
                                LEFT JOIN `contract` ON `contract`.`id` = `heated_object`.`contract_id` 
                                WHERE `heated_object`.`id` = :id';

                $entityName = 'heated-object';

                $propertyNames[] = 'idUpd';
                $propertyNames[] = 'nameHeatedObjUpd';
                $propertyNames[] = 'contractNumHeatedObjUpd';

                $dataColumns[] = 'id';
                $dataColumns[] = 'name';
                $dataColumns[] = 'contract_num';
                break;
            case 'select_device_by_id':
                $queryString = 'SELECT `device`.`id`, `device`.`device_num`, `device`.`is_boiler`, `device`.`is_heatmeter`, `device`.`heated_object_id` 
                                FROM `device` 
                                WHERE `device`.`id` = :id';

                $entityName = 'device';

                $propertyNames[] = 'idUpd';
                $propertyNames[] = 'numDeviceUpd';
                $propertyNames[] = 'isBoilerUpd';
				$propertyNames[] = 'isHeatmeterUpd';
                $propertyNames[] = 'HOidDeviceUpd';

                $dataColumns[] = 'id';
                $dataColumns[] = 'device_num';
                $dataColumns[] = 'is_boiler';
				$dataColumns[] = 'is_heatmeter';
                $dataColumns[] = 'heated_object_id';
                break;
            case 'select_devicevals_by_id':
                $queryString = 'SELECT `metering_values`.`id`, `metering_values`.`calc_value`, 
                                       `metering_values`.`calc_month`, `metering_values`.`calc_year`, 
                                       `metering_values`.`device_id`, `device`.`device_num`, 
                                       `metering_values`.`is_normative` 
                                FROM `metering_values` 
                                LEFT JOIN `device` ON `device`.`id` = `metering_values`.`device_id` 
                                WHERE `metering_values`.`id` = :id';

                $entityName = 'devicevals';

                $propertyNames[] = 'idUpd';
                $propertyNames[] = 'meteringvalsDevicevalsUpd';
                $propertyNames[] = 'calcperiodDevicevalsUpd';
                $propertyNames[] = null; //lame... cause of skipping iteration in for loop
                $propertyNames[] = 'deviceidDevicevalsUpd';
                $propertyNames[] = 'devicenumDevicevalsUpd';
                $propertyNames[] = 'isNormativeUpd';

                $dataColumns[] = 'id';
                $dataColumns[] = 'calc_value';
                $dataColumns[] = 'calc_month';
                $dataColumns[] = 'calc_year';
                $dataColumns[] = 'device_id';
                $dataColumns[] = 'device_num';
                $dataColumns[] = 'is_normative';
                break;
        }

        try {
            $this->createPDO();
            $queryEngine = new QueryEngine($this->pdo);
            $resultSet = $queryEngine->getResultSet($queryString, $params);

            $resultString = '{ ';
            $resultString .= '"entity" : "' . $entityName . '", ';

            while ($row = $resultSet->fetch(PDO::FETCH_LAZY)) {
                $resultString .= '"fields" : { ';

                $count = count($dataColumns);
                for ($i = 1; $i <= $count; $i++) {
                    if ($i != $count) {
                        if ($propertyNames[$i - 1] === 'calcperiodDevicevalsUpd') {
                            $resultString .= '"' . $propertyNames[$i - 1] . '" : "' .
                                    Utils::json_string_encode($row[$dataColumns[$i - 1]]) . "." .
                                    Utils::json_string_encode($row[$dataColumns[$i - 1 + 1]]) . '", ';
                            $i++;
                        } else {
                            $resultString .= '"' . $propertyNames[$i - 1] . '" : "' . Utils::json_string_encode($row[$dataColumns[$i - 1]]) . '", ';
                        }
                    } else {
                        $resultString .= '"' . $propertyNames[$i - 1] . '" : "' . Utils::json_string_encode($row[$dataColumns[$i - 1]]) . '"';
                    }
                }

                $resultString .= ' }';
            }

            $resultString .= ' }';
        } catch (PDOException $e) {
            $resultString = 'ERROR_PDO|' . $e->getMessage();
        }
        $this->destroyPDO();
        return $resultString;
    }
    
    /**
     * INSERT new record (contract->heated_object->device)
     * @param type $newrecordData
     * @return string
     */
    public function insertNewRecord($newrecordData) {
        $resultString = '';
        try {
            //key vars
            $contract_id = -1;
            $heated_object_id = -1;
            $device_id = -1;

            $this->createPDO();
            $queryEngine = new QueryEngine($this->pdo);

            /** CHECK FOR EXISTING contract * */
            //query string
            $queryString = 'SELECT `id` FROM `contract`
                            WHERE `contract_num` = :contract_num
                            ORDER BY `id` ASC
                            LIMIT 0, 20';
            $params = array();
            $params[] = new BoundParameter(':contract_num', Utils::formatValue($newrecordData->{'contract_num'}), PDO::PARAM_STR);

            $resultSet = $queryEngine->getResultSet($queryString, $params);
            $rows = $resultSet->fetchAll();
            $num_rows = count($rows);

            if ($num_rows > 0) {
                $resultString = 'ERROR_CONTRACT_EXISTS|' . $newrecordData->{'contract_num'};
                return $resultString;
            }

            /** CONTRACT * */
            $queryString = 'INSERT INTO `contract` (`contract_num`) 
                            VALUES (:contract_num)';
            $params = array();
            $params[] = new BoundParameter(':contract_num', Utils::formatValue($newrecordData->{'contract_num'}), PDO::PARAM_STR);

            $contract_id = $queryEngine->getLastInsertId($queryString, $params);

            /** HEATED OBJECTS * */
            foreach ($newrecordData->{'heated_objects'} as $value) {
                $queryString = 'INSERT INTO `heated_object` (`name`, `contract_id`) 
                                VALUES (:name, :contract_id)';
                $params = array();
                $params[] = new BoundParameter(':name', Utils::formatValue($value->{'name'}), PDO::PARAM_STR);
                $params[] = new BoundParameter(':contract_id', $contract_id, PDO::PARAM_INT);

                $heated_object_id = $queryEngine->getLastInsertId($queryString, $params);

                /** DEVICES * */
                foreach ($value->{'devices'} as $value) {
                    $queryString = 'INSERT INTO `device` (`device_num`, `is_boiler`, `is_heatmeter`, `heated_object_id`) 
                                    VALUES (:device_num, :is_boiler, :is_heatmeter, :heated_object_id)';
                    $params = array();
                    $params[] = new BoundParameter(':device_num', Utils::formatValue($value->{'device_num'}), PDO::PARAM_STR);
                    $params[] = new BoundParameter(':is_boiler', intval($value->{'is_boiler'}), PDO::PARAM_INT);
					$params[] = new BoundParameter(':is_heatmeter', intval($value->{'is_heatmeter'}), PDO::PARAM_INT);
                    $params[] = new BoundParameter(':heated_object_id', $heated_object_id, PDO::PARAM_INT);

                    $device_id = $queryEngine->getLastInsertId($queryString, $params);
                }
            }

            $resultString = 'Данные по договору: <b>' . $newrecordData->{'contract_num'} . '</b> успешно записаны в базу.';
        } catch (PDOException $e) {
            $resultString = 'ERROR_PDO|' . $e->getMessage();
        }
        $this->destroyPDO();
        return $resultString;
    }
    
    /**
     * INSERT
     * @param type $queryName
     * @param type $postData
     * @return string
     */
    public function insertData($queryName, $postData) {
        $resultString = '';

        $queryString = '';
        $params = array();

        switch ($queryName) {
            case 'insert_heated_object':
                $queryString = 'INSERT INTO `heated_object` (`name`, `contract_id`) 
                                VALUES (:name, :contract_id)';

                $params[] = new BoundParameter(':name', $postData['name'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':contract_id', $postData['contract_id'], PDO::PARAM_INT);

                $resultString = 'Данные по теплоустановке: <b>' . $postData['name'] . '</b> успешно записаны в базу.';
                break;
            case 'insert_device':
                $queryString = 'INSERT INTO `device` (`device_num`, `is_boiler`, `is_heatmeter`, `heated_object_id`) 
                                VALUES (:device_num, :is_boiler, :is_heatmeter, :heated_object_id)';

                $params[] = new BoundParameter(':device_num', $postData['device_num'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':is_boiler', $postData['is_boiler'], PDO::PARAM_INT);
				$params[] = new BoundParameter(':is_heatmeter', $postData['is_heatmeter'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':heated_object_id', $postData['heated_object_id'], PDO::PARAM_INT);

                $resultString = 'Данные по прибору учета: <b>' . $postData['device_num'] . '</b> успешно записаны в базу.';
                break;
            case 'insert_devicevals':
                $queryString = 'INSERT INTO `metering_values` (`device_id`, `calc_value`, `calc_month`, `calc_year`, `is_normative`) 
                                VALUES (:device_id, :calc_value, :calc_month, :calc_year, :is_normative)';

                $params[] = new BoundParameter(':device_id', $postData['device_id'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':calc_value', $postData['calc_value'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':calc_month', $postData['calc_month'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':calc_year', $postData['calc_year'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':is_normative', $postData['is_normative'], PDO::PARAM_INT);

                $resultString = 'Данные по показаниям ПУ (id): <b>' . $postData['device_id'] . '</b> успешно записаны в базу.';
                break;
        }

        try {
            $this->createPDO();
            $queryEngine = new QueryEngine($this->pdo);
            $lastInsertID = $queryEngine->getLastInsertId($queryString, $params);
        } catch (PDOException $e) {
            $resultString = 'ERROR_PDO|' . $e->getMessage();
        }
        $this->destroyPDO();
        return $resultString;
    }
    
    /**
     * UPDATE or DELETE
     * @param type $queryName
     * @param type $postData
     * @return string
     */
    public function changeData($queryName, $postData) {
        $resultString = '';

        $queryString = '';
        $params = array();

        switch ($queryName) {
            case 'update_contract':
                $queryString = 'UPDATE `contract` 
                                SET `contract_num` = :contract_num  
                                WHERE `contract`.`id` = :id';

                $params[] = new BoundParameter(':contract_num', $postData['contract_num'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Данные договора под номером id <b>' . $postData['id'] . '</b> успешно обновлены';
                break;
            case 'update_heated_object':
                $queryString = 'UPDATE `heated_object` 
                                SET `name` = :name  
                                WHERE `heated_object`.`id` = :id';

                $params[] = new BoundParameter(':name', $postData['name'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Данные теплоустановки под номером id <b>' . $postData['id'] . '</b> успешно обновлены';
                break;
            case 'update_device':
                $queryString = 'UPDATE `device` 
                                SET `device_num` = :device_num, `is_boiler` = :is_boiler, `is_heatmeter` = :is_heatmeter 
                                WHERE `device`.`id` = :id';

                $params[] = new BoundParameter(':device_num', $postData['device_num'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':is_boiler', $postData['is_boiler'], PDO::PARAM_INT);
				$params[] = new BoundParameter(':is_heatmeter', $postData['is_heatmeter'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Данные прибора учета под номером id <b>' . $postData['id'] . '</b> успешно обновлены';
                break;
            case 'update_devicevals':
                $queryString = 'UPDATE `metering_values` 
                                SET `calc_value` = :calc_value, `calc_month` = :calc_month, `calc_year` = :calc_year, `is_normative` = :is_normative
                                WHERE `metering_values`.`id` = :id';

                $params[] = new BoundParameter(':calc_value', $postData['calc_value'], PDO::PARAM_STR);
                $params[] = new BoundParameter(':calc_month', $postData['calc_month'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':calc_year', $postData['calc_year'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':is_normative', $postData['is_normative'], PDO::PARAM_INT);
                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Данные показаний ПУ под номером id <b>' . $postData['id'] . '</b> успешно обновлены';
                break;
            case 'delete_contract':
                $queryString = "DELETE `contract`, `heated_object`, `device`, `metering_values` 
                                FROM `contract` 
                                LEFT JOIN `heated_object` ON `heated_object`.`contract_id` = `contract`.`id` 
                                LEFT JOIN `device` ON `device`.`heated_object_id` = `heated_object`.`id` 
                                LEFT JOIN `metering_values` ON `metering_values`.`device_id` = `device`.`id` 
                                WHERE `contract`.`id` = :id";

                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Договор под номером id: <b>' . $postData['id'] . '</b> и связанные с ним объекты успешно удалены.';
                break;
            case 'delete_heated_object':
                $queryString = "DELETE `heated_object`, `device`, `metering_values` 
                                FROM `heated_object`  
                                LEFT JOIN `device` ON `device`.`heated_object_id` = `heated_object`.`id` 
                                LEFT JOIN `metering_values` ON `metering_values`.`device_id` = `device`.`id` 
                                WHERE `heated_object`.`id` = :id";

                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Теплоустановка под номером id: <b>' . $postData['id'] . '</b> и связанные с ней объекты успешно удалены.';
                break;
            case 'delete_device':
                $queryString = "DELETE `device`, `metering_values` 
                                FROM `device`  
                                LEFT JOIN `metering_values` ON `metering_values`.`device_id` = `device`.`id` 
                                WHERE `device`.`id` = :id";

                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Прибор учета под номером id: <b>' . $postData['id'] . '</b> и связанные с ним показания успешно удалены.';
                break;
            case 'delete_devicevals':
                $queryString = "DELETE  
                                FROM `metering_values`  
                                WHERE `metering_values`.`id` = :id";

                $params[] = new BoundParameter(':id', $postData['id'], PDO::PARAM_INT);

                $resultString = 'Показания ПУ под номером id: <b>' . $postData['id'] . '</b> успешно удалены.';
                break;
        }

        try {
            $this->createPDO();
            $queryEngine = new QueryEngine($this->pdo);
            $queryEngine->executeQuery($queryString, $params);
        } catch (PDOException $e) {
            $resultString = 'ERROR_PDO|' . $e->getMessage();
        }
        $this->destroyPDO();
        return $resultString;
    }

}
