<pre>
<?php
//error_reporting(E_ALL || ~E_NOTICE);
require_once("db_config.php");

function check_connect()
{
    if (mysqli_connect_error()) {
        $error = mysqli_connect_error() . "\\n";
        $error = $error . "连接失败...\\n";
        $error = $error . "请检查数据库连接以及PHP配置,再点确定刷新次页面";
        //echo '<script> {window.alert("' . $error . '");location.href="index_php.php"} </script>';
        report_error($error);
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

/*name: patientName,
sex: patientSex,
age: patientAge,
weight: patientWeight,
hight: patientHeight,
lang: patientLung,
id: patientID,
radios: JSON.stringify(radioAnswers)*/

$_name = $_POST["name"];
$_age = $_POST["age"];
$_sex = $_POST["sex"];
$_weight = $_POST["weight"];
$_hight = $_POST["hight"];
$_lung = $_POST["lung"];
$_id = (string)$_POST["id"];
$_radios = json_decode($_POST["radios"]);

/*$_name = 'nn';
$_age = 12;
$_sex = '男';
$_weight = 12;
$_hight = 12;
$_lung = 12;
$_id = '111111111111111111';
$_radios = array(1,1,1,1,1,1,1,1,1,1,1,1);*/

$final_answer_array = array_merge(array($_name, $_sex), $_radios, array($_id), array($_age, $_hight, $_weight, $_lung));

$q_quarry = array('a', 'b', 'c', 'd', 'e', 'f');
//$result_array = array('轻度功能障碍', '中度功能障碍', '重度功能障碍', '极重度功能障碍', '完全功能障碍,应详细检查受试对象有无夸大症状');

$finalResult = false;

$mysqli = new mysqli($mysql_server_name, $mysql_username, $mysql_password); //default port 3306
//设置编码
$mysqli->set_charset("utf8"); //或者 $mysqli->query("set names 'utf8'")
//面向对象的昂视屏蔽了连接产生的错误,需要通过函数来判断
$table_info = "(
        private_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        patien_id varchar(20) NOT NULL,
        age varchar(4) NOT NULL,
        name varchar(100) NOT NULL,
        sex varchar(2) NOT NULL,
        weight varchar(4) NOT NULL,
        height varchar(4) NOT NULL,
        lung varchar(4) NOT NULL,
        pain_6 int(11) NOT NULL,
        pain_1 int(11) NOT NULL,
        nervous int(11) NOT NULL,
        staynow int(11) NOT NULL,
        actstaus int(11) NOT NULL,
        clothfeel int(11) NOT NULL,
        unhappy int(11) NOT NULL,
        backpain int(11) NOT NULL,
        workstudy int(11) NOT NULL,
        appareance int(11) NOT NULL,
        medicine int(11) NOT NULL,
        housework int(11) NOT NULL,
        peace int(11) NOT NULL,
        relationship int(11) NOT NULL,
        finacial int(11) NOT NULL,
        unhappy_6 int(11) NOT NULL,
        askleave int(11) NOT NULL,
        playtime int(11) NOT NULL,
        staisfiedback int(11) NOT NULL,
        happy_6 int(11) NOT NULL,
        satisfiedmedical int(11) NOT NULL,
        sametreat int(11) NOT NULL,
        submmitdate DATETIME
        )ENGINE=InnoDB";

$data_label = "patien_id,age,name,sex,weight,height,lung,pain_6,pain_1,nervous,staynow,actstaus,clothfeel,unhappy,backpain,workstudy,appareance,medicine,housework,peace,relationship,finacial,unhappy_6,askleave,playtime,staisfiedback,happy_6,satisfiedmedical,sametreat,submmitdate";
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
            //$_socre_datas = $PF_sum . "," . $SF_sum . "," . $RP_sum . "," . $RE_sum . "," . $MH_sum . "," . $VT_sum . "," . $HT_sum . "," . $GH_sum . "," . $BP_sum;
            //$_string_numbers = implode(",", $_numbers);
            $_data_time = 'now()';
            $final_data = $_id . "," . $_age . ",'" . $_name . "'" . ",'" . $_sex . "'" . "," . $_weight . "," . $_hight . "," . $_lung . "," .
                $_string_radios . "," . $_data_time;
            //insert_data($mysqli, $mysql_table, $data_label, $final_data);
            echo $final_data;
            $file_name = $file_path . $_name . '_SRS22_' . date('Y-m-d') . time() . ".html";
            echo copy($tamplate_file, $file_name);
            $html_dom = new DOMDocument;
            $html_dom->loadHTMLFile($file_name);
            for ($i = 1; $i <= 29; $i++) {
                if (($i >= 1 and $i <= 2) or ($i >= 25 and $i <= 29)) {
                    $q_tmp = $html_dom->getElementById("q" . $i);
                    //echo $final_answer_array[$i-1];
                    $q_tmp->setAttribute('placeholder', $final_answer_array[$i - 1]);
                } elseif (2 < $i and $i < 25) {
                    $q_tmp = $html_dom->getElementById("q" . $i . $q_quarry[$final_answer_array[$i - 1]]);
                    $q_tmp->setAttribute('checked', 'checked');
                }
            }
            $q_tmp = $html_dom->getElementsByTagName("title");
            foreach ($q_tmp as $title_tmp) {
                //echo "In  it";
                $title_tmp->textContent = $_name . "_SRS22调查表";
            }
            $q_tmp = $html_dom->getElementById("q30");
            $q_tmp->setAttribute('placeholder', date('Y-m-d', time()));
            $html_dom->saveHTMLFile($file_name);
        } else {
            echo "未创建表,无法操作！\n";
        }
    } else {
        echo "无法创建数据库,无法操作！\n";
    }
}
$mysqli->close();

?>                             