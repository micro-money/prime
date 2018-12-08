<?php
$skip_frontend_config = true; // Пропускаем загрузку ненужных классов
require_once('../config.php');

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');

$headers = getallheaders();
//if ($headers["Content-Type"] == "application/json; charset=utf-8")
$data_in = json_decode(file_get_contents("php://input"), true) ?: [];

f_log("Тело запроса:\r\n" . file_get_contents("php://input") . "\r\nРасшифровка входящего JSON: \r\n" . print_r($data_in, true) . "\r\n", 'API');

$result = [];

// Функция досрочного завершения с выводом ошибки
function api_exit ($error_txt, $success = false) {
    global $data_in, $result, $data_in_txt;
    if (!empty($error_txt)) {
        $result['error'] = true;
        $result['error_msg'] .= $error_txt;
    }
    f_log("Ответный массив данных: \r\n" . print_r($result, true) . "\r\n", 'API');
    die (json_encode($result));
}

// Функция проверки обязательных параметров
function required_parameters_check ($parameters_required) {
    global $data_in, $result;
    // Проверка наличия обязательных параметров
    $required_error_parameters = Array();
    if (!empty($parameters_required) || count($parameters_required)) {
        foreach ($parameters_required as $parameter_name) {
            // Если параметр не пришел - заносим в массив его как ошибочный
            if (empty( $data_in[$parameter_name] )) $required_error_parameters[] = $parameter_name;
        }
        if (!empty($required_error_parameters)) api_exit ('Required parameter: ' . implode (', ', $required_error_parameters) );
    }
}

// Проверка, запрошен какой-либо метод API
if (empty( $data_in['method'] )) api_exit ('API method exists');


// Выполнение методов API
switch ( $data_in['method'] ) {

    case 'app_status':
        required_parameters_check(['Id']);
		f_log('Id = ' . $data_in['Id'] . "\r\n", 'API');
        $CrmTextStatus = mysql_real_escape_string($data_in['Status']);
        if (db_request("UPDATE `leads`
                        SET `CrmTextStatus` = '$CrmTextStatus'
                        WHERE `CrmId` = '{$data_in['Id']}'")) $result['result'] = 'ok';
        else api_exit ('Unknown App Id');
        break;

    default:
        api_exit ('Unknown API method');
}

api_exit ('', true);