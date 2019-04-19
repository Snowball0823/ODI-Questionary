<pre>
<?php
error_reporting(E_ALL || ~E_NOTICE);
require_once("db_config.php");

function check_connect()
{
    if (mysqli_connect_error()) {
        $error = mysqli_connect_error() . "\\n";
        $error = $error . "连接失败...\\n";
        $error = $error . "请检查数据库连接以及PHP配置，再点确定刷新次页面";
        echo '<script> {window.alert("' . $error . '");location.href="index_php.php"} </script>';
        return false;
    }
    return true;
}

function check_database($mysqli, $database)
{
    $sql_usedatabase = "USE " . $database;
    $result = _execute_order($mysqli, $sql_usedatabase);
    $finalDecision = true;
    //echo $sql_usedatabase . "\n";
    if ($result != true) {
        $error = $database . ",此数据库不存在" . "\\n";
        $error = $error . "Error:" . $mysqli->error . "\\n";
        $error = $error . "正在创建...";
        report_error($error);
        if (creat_database($mysqli, $database)) {
            $finalDecision = check_database($mysqli, $database);
        }
    }
    return $finalDecision;
}

function creat_database($mysqli, $database)
{
    $sql_creatdatabase = "CREATE DATABASE " . $database;
    $result = _execute_order($mysqli, $sql_creatdatabase);
    $finalDecision = true;
    if ($result != true) {
        $error = "建库失败！" . "\\n";
        $error = $error . "Error:" . $mysqli->error . "\\n";
        $error = $error . "正在重新创建...";
        report_error($error);
        creat_database($mysqli, $database);
    }
    return $finalDecision;
}

function check_table($mysqli, $table)
{
    $sql_usetable = "desc " . $table;
    $result = _execute_order($mysqli, $sql_usetable);
    if ($result != true) {
        $error = $table . ",此表不存在" . "\\n";
        $error = $error . "Error:" . $mysqli->error . "\\n";
        $error = $error . "正在创建...";
        report_error($error);
    }
    return $result;
}

function creat_table($mysqli, $table,  $table_info)
{
    $sql_creattable = "CREATE TABLE " . $table . $table_info;
    $result = _execute_order($mysqli, $sql_creattable);
    if ($result != true) {
        $error = "建表失败！" . "\\n";
        $error = $error . "Error:" . $mysqli->error . "\\n";
        $error = $error . "正在重新创建...";
        report_error($error);
        creat_table($mysqli, $table,  $table_info);
    }
}

function insert_data($mysqli, $table, $label, $data)
{
    $sql_insertdata = "INSERT INTO " . $table . "(" . $label . ") 
    VALUES (" . $data . ")";
    echo $sql_insertdata . "\n";
    $result = _execute_order($mysqli, $sql_insertdata);
    if ($result != true) {
        $error = "插入数据失败！" . "\\n";
        $error = $error . "Error:" . $mysqli->error . "\\n";
        report_error($error);
    }
    return $result;
}

function show_result($result)
{
    try {
        _show_related_result($result);
    } catch (Exception $e) {
        _show_array_result($result);
    }
}

function report_error($error)
{
    echo '<script> confirm("' . $error . '")</script>';
}

function _execute_order($mysqli, $order)
{
    $result = $mysqli->query($order);
    return $result;
}

function _show_related_result($result)
{
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $x => $x_value) {
            echo  $x . ", Value=" . $x_value;
            echo "<br>";
        }
        echo "<br>";
    }
}

function _show_array_result($result)
{
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $x) {
            echo  "Value= " . $x;
            echo "<br>";
        }
        echo "<br>";
    }
}


$_name = $_POST["name"];
$_sex = $_POST["sex"];
$_id = $_POST["id"];
$_radios = json_decode($_POST["radios"]);
$_numbers = json_decode($_POST["numbers"]);

$finalResult = false;

$mysqli = new mysqli($mysql_server_name, $mysql_username, $mysql_password); //default port 3306
//设置编码
$mysqli->set_charset("utf8"); //或者 $mysqli->query("set names 'utf8'")
//面向对象的昂视屏蔽了连接产生的错误，需要通过函数来判断
$table_info = "(
        patien_id  int(11) UNSIGNED NOT NULL PRIMARY KEY,
        name varchar(100) NOT NULL,
        sex  varchar(2) NOT NULL,
        pain int(11) NOT NULL,
        selfwork int(11) NOT NULL,
        carrything int(11) NOT NULL,
        walk int(11) NOT NULL,
        sit int(11) NOT NULL,
        standup int(11) NOT NULL,
        sleep int(11) NOT NULL,
        sexlife int(11) NOT NULL,
        sociallife int(11) NOT NULL,
        travel int(11) NOT NULL,
        homework int(11) NOT NULL,
        walkpain int(11) NOT NULL,
        sitpain int(11) NOT NULL,
        stanpain int(11) NOT NULL,
        paininday int(11) NOT NULL,
        submmitdate DATETIME
        )ENGINE=InnoDB";
$data_label = "patien_id,name,sex,pain,selfwork,carrything,walk,sit,standup,sleep,sexlife,sociallife,travel,homework,walkpain,sitpain,stanpain,paininday,submmitdate";
if (check_connect()) {
    if (check_database($mysqli, $mysql_database)) {
        if (check_table($mysqli, $mysql_table) != true) {
            creat_table($mysqli, $mysql_table,  $table_info);
            $finalResult = true;
        } else {
            $finalResult = true;
        }
        if ($finalResult) {
            $_string_radios = implode(",", $_radios);
            $_string_numbers = implode(",", $_numbers);
            $_data_time = 'now()';
            $final_data = $_id . ",'" . $_name . "'" . ",'" . $_sex . "'" . "," .
                $_string_radios . "," . $_string_numbers . "," . $_data_time;
            //insert_data($mysqli, $mysql_table, $data_label, $final_data);
            echo $final_data;
        } else {
            echo "未创建表，无法操作！\n";
        }
    } else {
        echo "无法创建数据库，无法操作！\n";
    }
}
$mysqli->close();
?>                             