<?php
/**
 *  ORG-IPU back-end
 *  Main Entrance
 *  special "interface" for calling web-application methods 
 *  kinda "Main" class
 * @author SAV2
 * @since 25.03.2020
 * @version 0.2.0
 */

use utils\TemplateProvider;
use utils\WorkspaceKeeper;
use utils\Utils;
use dbcengine\DBEngine;
use ws\ConsumptionSOAP;

//class autoloader
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

//start session
session_start();

//Entrance
if (Utils::postValueIsValid(filter_input(INPUT_GET, 'action'))) {
    switch (filter_input(INPUT_GET, 'action')) {
        case 'login':
            if (Utils::postValueIsValid(filter_input(INPUT_POST, 'id')) &&
                    Utils::postValueIsValid(filter_input(INPUT_POST, 'usr')) &&
                    Utils::postValueIsValid(filter_input(INPUT_POST, 'pwd')) &&
                    filter_input(INPUT_POST, 'id') === 'isuservalid') {
                $workspace = new WorkspaceKeeper();
                echo $workspace->doLogin(filter_input(INPUT_POST, 'usr'), filter_input(INPUT_POST, 'pwd'));
            } else {
                session_destroy();
                echo 'ERROR_POSTDATA_INCORRECT';
            }
            break;
        case 'logout':
            $workspace = new WorkspaceKeeper();
            $workspace->doLogout();
            break;
        case 'load_workspace':
            if (Utils::postValueIsValid(filter_input(INPUT_POST, 'userid')) &&
                    Utils::postValueIsValid(filter_input(INPUT_POST, 'userrole'))) {
                $workspace = new WorkspaceKeeper();
                echo $workspace->loadWorkspace(filter_input(INPUT_POST, 'userid'), filter_input(INPUT_POST, 'userrole'));
            } else {
                echo '0';
            }
            break;
        case 'keep_workspace':
            $workspace = new WorkspaceKeeper();
            echo $workspace->keepWorkspace();
            break;
        case 'draw_panel':
            $templateProvider = new TemplateProvider();
            echo $templateProvider->loadTemplate(filter_input(INPUT_POST, 'tmplname'));
            break;
        case 'select_contracts':
            //3 -- for Admin usage | 1 -- for guest usage
            if (Utils::checkPermission(3) || Utils::checkPermission(1)) {
                $postData = array();
                $postData['page']           = intval(filter_input(INPUT_POST, 'page'));
                $postData['per_page']       = 25;
                $postData['start_position'] = $postData['per_page'] * $postData['page'];

                $postData['contract_num']   = Utils::createRegExp(filter_input(INPUT_POST, 'contract_num'), Utils::STARTS_FROM);
                $postData['contract_id']    = Utils::createRegExp(filter_input(INPUT_POST, 'id'), Utils::EQUALS);

                $dbEngine = new DBEngine();
                echo $dbEngine->selectData('select_contracts', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'select_heated_objects':
            //3 -- for Admin usage | 1 -- for guest usage
            if (Utils::checkPermission(3) || Utils::checkPermission(1)) {
                $postData = array();
                $postData['page']               = intval(filter_input(INPUT_POST, 'page'));
                $postData['per_page']           = 25;
                $postData['start_position']     = $postData['per_page'] * $postData['page'];

                $postData['heated_object_id']   = Utils::createRegExp(filter_input(INPUT_POST, 'id'), Utils::EQUALS);
                $postData['heated_object_name'] = Utils::createRegExp(filter_input(INPUT_POST, 'name'), Utils::CONTAINS);
                $postData['contract_num']       = Utils::createRegExp(filter_input(INPUT_POST, 'contractnum'), Utils::STARTS_FROM);

                $dbEngine = new DBEngine();
                echo $dbEngine->selectData('select_heated_objects', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'select_devices':
            //3 -- for Admin usage | 1 -- for guest usage
            if (Utils::checkPermission(3) || Utils::checkPermission(1)) {
                $postData = array();
                $postData['page']               = intval(filter_input(INPUT_POST, 'page'));
                $postData['per_page']           = 25;
                $postData['start_position']     = $postData['per_page'] * $postData['page'];

                $postData['device_id']          = Utils::createRegExp(filter_input(INPUT_POST, 'id'), Utils::EQUALS);
                $postData['device_num']         = Utils::createRegExp(filter_input(INPUT_POST, 'device_num'), Utils::STARTS_FROM);
                $postData['heated_object_name'] = Utils::createRegExp(filter_input(INPUT_POST, 'heated_object_name'), Utils::CONTAINS);
                $postData['heated_object_id']   = Utils::createRegExp(filter_input(INPUT_POST, 'heated_object_id'), Utils::EQUALS);
                $postData['contract_num']       = Utils::createRegExp(filter_input(INPUT_POST, 'contractnum'), Utils::STARTS_FROM);
				$postData['is_boiler']   		= Utils::createRegExp(filter_input(INPUT_POST, 'is_boiler'), Utils::EQUALS);
				$postData['is_heatmeter']   	= Utils::createRegExp(filter_input(INPUT_POST, 'is_heatmeter'), Utils::EQUALS);

                $dbEngine = new DBEngine();
                echo $dbEngine->selectData('select_devices', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'select_devicevals':
            //3 -- for Admin usage | 1 -- for guest usage
            if (Utils::checkPermission(3) || Utils::checkPermission(1)) {
                $postData = array();
                $postData['page']               = intval(filter_input(INPUT_POST, 'page'));
                $postData['per_page']           = 25;
                $postData['start_position']     = $postData['per_page'] * $postData['page'];

                $postData['metering_values_id'] = Utils::createRegExp(filter_input(INPUT_POST, 'id'), Utils::EQUALS);
                $postData['device_id']          = Utils::createRegExp(filter_input(INPUT_POST, 'device_id'), Utils::EQUALS);
                $postData['device_num']         = Utils::createRegExp(filter_input(INPUT_POST, 'device_num'), Utils::STARTS_FROM);
                $postData['calc_month']         = Utils::createRegExp(filter_input(INPUT_POST, 'calc_month'), Utils::EQUALS);
                $postData['calc_year']          = Utils::createRegExp(filter_input(INPUT_POST, 'calc_year'), Utils::EQUALS);
                $postData['heated_object_name'] = Utils::createRegExp(filter_input(INPUT_POST, 'heated_object_name'), Utils::CONTAINS);
                $postData['contract_num']       = Utils::createRegExp(filter_input(INPUT_POST, 'contract_num'), Utils::STARTS_FROM);

                $dbEngine = new DBEngine();
                echo $dbEngine->selectData('select_devicevals', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'consumptions':
            //3 -- for Admin usage | 1 -- for guest usage
            if (Utils::checkPermission(3) || Utils::checkPermission(1)) {
                $postData = array();
                $postData['page']                = intval(filter_input(INPUT_POST, 'page'));
                $postData['per_page']            = 25;
                $postData['start_position']      = $postData['per_page'] * $postData['page'];

                $postData['device_id']           = Utils::createRegExp(filter_input(INPUT_POST, 'id'), Utils::EQUALS);
                $postData['device_num']          = Utils::createRegExp(filter_input(INPUT_POST, 'device_num'), Utils::STARTS_FROM);
                $postData['heated_object_name']  = Utils::createRegExp(filter_input(INPUT_POST, 'heated_object_name'), Utils::CONTAINS);
                $postData['heated_object_id']    = Utils::createRegExp(filter_input(INPUT_POST, 'heated_object_id'), Utils::EQUALS);
                $postData['contract_num']        = Utils::createRegExp(filter_input(INPUT_POST, 'contractnum'), Utils::STARTS_FROM);
                $postData['calc_month']          = intval(filter_input(INPUT_POST, 'calc_month'));
                $postData['calc_year']           = intval(filter_input(INPUT_POST, 'calc_year'));
                $postData['hide_normative_vals'] = intval(filter_input(INPUT_POST, 'hide_normative_vals'));

                $dbEngine = new DBEngine();
                echo $dbEngine->selectConsumption($postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'select_contract_by_id':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $dbEngine = new DBEngine();
                echo $dbEngine->selectDataByID('select_contract_by_id', intval(filter_input(INPUT_POST, 'id')));
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'select_heated_object_by_id':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $dbEngine = new DBEngine();
                echo $dbEngine->selectDataByID('select_heated_object_by_id', intval(filter_input(INPUT_POST, 'id')));
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'select_device_by_id':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $dbEngine = new DBEngine();
                echo $dbEngine->selectDataByID('select_device_by_id', intval(filter_input(INPUT_POST, 'id')));
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'select_devicevals_by_id':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $dbEngine = new DBEngine();
                echo $dbEngine->selectDataByID('select_devicevals_by_id', intval(filter_input(INPUT_POST, 'id')));
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'insert_newrecord':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $newrecordData = json_decode(filter_input(INPUT_POST, 'newRecordJSON'));

                $dbEngine = new DBEngine();
                echo $dbEngine->insertNewRecord($newrecordData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'insert_heated_object':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['name']        = Utils::formatValue(filter_input(INPUT_POST, 'name'));
                $postData['contract_id'] = intval(filter_input(INPUT_POST, 'contract_id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->insertData('insert_heated_object', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'insert_device':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['device_num']       = Utils::formatValue(filter_input(INPUT_POST, 'device_num'));
                $postData['is_boiler']        = intval(filter_input(INPUT_POST, 'is_boiler'));
				$postData['is_heatmeter']     = intval(filter_input(INPUT_POST, 'is_heatmeter'));
                $postData['heated_object_id'] = intval(filter_input(INPUT_POST, 'heated_object_id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->insertData('insert_device', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'insert_devicevals':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['device_id']    = intval(filter_input(INPUT_POST, 'device_id'));
                $postData['calc_value']   = Utils::formatValue(filter_input(INPUT_POST, 'calc_value'));
                $postData['calc_month']   = intval(filter_input(INPUT_POST, 'calc_month'));
                $postData['calc_year']    = intval(filter_input(INPUT_POST, 'calc_year'));
                $postData['is_normative'] = intval(filter_input(INPUT_POST, 'is_normative'));
				
				if ($postData['calc_month'] > 0 && 
					$postData['calc_month'] < 13 && 
					$postData['calc_year'] > 0) 
				{				
					$dbEngine = new DBEngine();
					echo $dbEngine->insertData('insert_devicevals', $postData);
				} else {
					echo 'ERROR_PDO|Пожалуйста выберите корректную дату показаний прибора учета';
				}
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'update_contract':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['contract_num'] = Utils::formatValue(filter_input(INPUT_POST, 'contract_num'));
                $postData['id']           = intval(filter_input(INPUT_POST, 'id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->changeData('update_contract', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'update_heated_object':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['name'] = Utils::formatValue(filter_input(INPUT_POST, 'name'));
                $postData['id']   = intval(filter_input(INPUT_POST, 'id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->changeData('update_heated_object', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'update_device':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['device_num'] 	= Utils::formatValue(filter_input(INPUT_POST, 'device_num'));
                $postData['is_boiler']  	= intval(filter_input(INPUT_POST, 'is_boiler'));
				$postData['is_heatmeter']  	= intval(filter_input(INPUT_POST, 'is_heatmeter'));
                $postData['id']         	= intval(filter_input(INPUT_POST, 'id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->changeData('update_device', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'update_devicevals':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['calc_value']   = Utils::formatValue(filter_input(INPUT_POST, 'calc_value'));
                $postData['calc_month']   = intval(filter_input(INPUT_POST, 'calc_month'));
                $postData['calc_year']    = intval(filter_input(INPUT_POST, 'calc_year'));
                $postData['is_normative'] = intval(filter_input(INPUT_POST, 'is_normative'));
                $postData['id']           = intval(filter_input(INPUT_POST, 'id'));
				
				if ($postData['calc_month'] > 0 && 
					$postData['calc_month'] < 13 && 
					$postData['calc_year'] > 0) 
				{				
					$dbEngine = new DBEngine();
					echo $dbEngine->changeData('update_devicevals', $postData);
				} else {
					echo 'ERROR_PDO|Пожалуйста выберите корректную дату показаний прибора учета';
				}
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'delete_contract':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['id'] = intval(filter_input(INPUT_POST, 'id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->changeData('delete_contract', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'delete_heated_object':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['id'] = intval(filter_input(INPUT_POST, 'id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->changeData('delete_heated_object', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'delete_device':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['id'] = intval(filter_input(INPUT_POST, 'id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->changeData('delete_device', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'delete_devicevals':
            //3 -- for Admin usage
            if (Utils::checkPermission(3)) {
                $postData = array();
                $postData['id'] = intval(filter_input(INPUT_POST, 'id'));

                $dbEngine = new DBEngine();
                echo $dbEngine->changeData('delete_devicevals', $postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
        case 'consumptionSOAP':
            //3 -- for Admin usage | 1 -- for guest usage
            if (Utils::checkPermission(3) || Utils::checkPermission(1)) {
                $postData = array();
                $postData['month'] = filter_input(INPUT_POST, 'month');
                $postData['year']  = filter_input(INPUT_POST, 'year');

                echo ConsumptionSOAP::writeDataIntoXLSX($postData);
            } else {
                echo 'ERROR_ACCESS_DENIED';
            }
            break;
    }
}
