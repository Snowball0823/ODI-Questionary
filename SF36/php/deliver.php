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


$_name = $_POST["name"];
$_age = $_POST["age"];
$_sex = $_POST["sex"];
$_id = (string)$_POST["id"];
$_radios = json_decode($_POST["radios"]);
//$_numbers = json_decode($_POST["numbers"]);

$socres_one = array(1, 2, 3, 4, 5, 6);
$socres_two = array(1, 2.2, 3.1, 4.2, 5.4, 6);
$socres_three = array(1, 2.25, 3.5, 4.75, 6);
//6, 5.4, 4.2, 3.1, 2.2, 1
//6, 4.75, 3.5, 2.25, 1.0
$PF_sum = 0;
for ($i = 2; $i <= 11; $i++) {
    $PF_sum += $socres_one[$_radios[$i]];
}
$PF_sum = ($PF_sum - 10) / 20 * 100;
$RP_sum = 0;
for ($i = 12; $i <= 15; $i++) {
    $RP_sum += $socres_one[$_radios[$i]];
}
$RP_sum = ($RP_sum - 4) / 4 * 100;
$BP_sum = 0;
//20,21
$BP_sum += $socres_two[$_radios[20]];
if ($_radios[21] == 4 && $_radios[20] == 5) {
    $BP_sum += $socres_one[5];
} else {
    $BP_sum += $socres_one[$_radios[21]];
}
$BP_sum = ($BP_sum - 2) / 10 * 100;
$GH_sum = 0;
$GH_sum += $socres_one[$_radios[0]];
for ($i = 32; $i <= 35; $i++) {
    $GH_sum += $socres_one[$_radios[$i]];
}
$GH_sum = ($GH_sum - 5) / 20 * 100;
$VT_sum = 0;
//22,26,28,30
$vt_index_array = array(22, 26, 28, 30);
foreach ($vt_index_array as $i) {
    $VT_sum += $socres_one[$_radios[$i]];
}
$VT_sum = ($VT_sum - 4) / 20 * 100;
$SF_sum = 0;
//19,23
$SF_sum += $socres_one[$_radios[19] + 1];
$SF_sum += $socres_one[$_radios[23]];
$SF_sum = ($SF_sum - 2) / 8 * 100;
$RE_sum = 0;
for ($i = 16; $i <= 18; $i++) {
    $RE_sum += $socres_one[$_radios[$i]];
}
$RE_sum = ($RE_sum - 3) / 3 * 100;
$MH_sum = 0;
$mh_index_array = array(23, 24, 25, 27, 29);
foreach ($mh_index_array as $i) {
    $MH_sum += $socres_one[$_radios[$i]];
}
$MH_sum = ($MH_sum - 5) / 25 * 100;
$HT_sum = $socres_one[$_radios[1]];
$HT_sum = ($HT_sum - 1) / 4 * 100;
$sum_array = array($PF_sum, $SF_sum, $RP_sum, $RE_sum, $MH_sum, $VT_sum, $HT_sum, $GH_sum, $BP_sum);
$final_answer_array = array_merge(array($_name, $_sex), $_radios, array($_age), array($_id), $sum_array);
print_r($final_answer_array);

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
        health int(11) NOT NULL,
        status int(11) NOT NULL,
        tiredsport int(11) NOT NULL,
        fitsport int(11) NOT NULL,
        life int(11) NOT NULL,
        upstairs int(11) NOT NULL,
        uponestair int(11) NOT NULL,
        actions int(11) NOT NULL,
        walk_1500 int(11) NOT NULL,
        work_1000 int(11) NOT NULL,
        work_100 int(11) NOT NULL,
        shower int(11) NOT NULL,
        health_subworktime int(11) NOT NULL,
        health_wantedthing int(11) NOT NULL,
        health_wantedwork int(11) NOT NULL,
        health_finshwork int(11) NOT NULL,
        mood_subworktime int(11) NOT NULL,
        mood_wantedthing int(11) NOT NULL,
        mood_careful int(11) NOT NULL,
        mood_influnce int(11) NOT NULL,
        pain int(11) NOT NULL,
        pain_influence int(11) NOT NULL,
        lifesatisfied int(11) NOT NULL,
        sensitive_man int(11) NOT NULL,
        badmood int(11) NOT NULL,
        quite int(11) NOT NULL,
        fullenergy int(11) NOT NULL,
        downmood int(11) NOT NULL,
        tired int(11) NOT NULL,
        happy int(11) NOT NULL,
        hate int(11) NOT NULL,
        unhealthinfluence int(11) NOT NULL,
        sick int(11) NOT NULL,
        healthytoo int(11) NOT NULL,
        healthbebad int(11) NOT NULL,
        healthgood int(11) NOT NULL,
        PF float(11,8) NOT NULL,
        SF float(11,8) NOT NULL,
        RP float(11,8) NOT NULL,
        RE float(11,8) NOT NULL,
        MH float(11,8) NOT NULL,
        VT float(11,8) NOT NULL,
        HT float(11,8) NOT NULL,
        GH float(11,8) NOT NULL,
        BP float(11,8) NOT NULL,
        submmitdate DATETIME
        )ENGINE=InnoDB";

$data_label = "patien_id,age,name,sex,health,status,tiredsport,fitsport,life,upstairs,uponestair,actions,walk_1500,work_1000,work_100,shower,health_subworktime,health_wantedthing,health_wantedwork,health_finshwork,mood_subworktime,mood_wantedthing,mood_careful,mood_influnce,pain,pain_influence,lifesatisfied,sensitive_man,badmood,quite,fullenergy,downmood,tired,happy,hate,unhealthinfluence,sick,healthytoo,healthbebad,healthgood,PF,SF,RP,RE,MH,VT,HT,GH,BP,submmitdate";
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
            $_socre_datas = $PF_sum . "," . $SF_sum . "," . $RP_sum . "," . $RE_sum . "," . $MH_sum . "," . $VT_sum . "," . $HT_sum . "," . $GH_sum . "," . $BP_sum;
            //$_string_numbers = implode(",", $_numbers);
            $_data_time = 'now()';
            $final_data = $_id . "," . $_age . ",'" . $_name . "'" . ",'" . $_sex . "'" . "," .
                $_string_radios . "," . $_socre_datas . "," . $_data_time;
            insert_data($mysqli, $mysql_table, $data_label, $final_data);
            echo $final_data;
            $file_name = $file_path . $_name . '_SF36_' . date('Y-m-d') . time() . ".html";
            echo copy($tamplate_file, $file_name);
            $html_dom = new DOMDocument;
            $html_dom->loadHTMLFile($file_name);
            for ($i = 1; $i <= 40; $i++) {
                if ($i == 1 or $i == 2 or $i == 39 or $i == 40) {
                    $q_tmp = $html_dom->getElementById("q" . $i);
                    //echo $final_answer_array[$i-1];
                    $q_tmp->setAttribute('placeholder', $final_answer_array[$i - 1]);
                } elseif (2 < $i and $i < 39) {
                    $q_tmp = $html_dom->getElementById("q" . $i . $q_quarry[$final_answer_array[$i - 1]]);
                    $q_tmp->setAttribute('checked', 'checked');
                }
            }
            for ($i = 41; $i < 50; $i++) {
                $q_tmp = $html_dom->getElementById("q" . $i);
                //echo $final_answer_array[$i-1];
                $q_tmp->setAttribute('placeholder', $final_answer_array[$i - 1]);
            }

            $q_tmp = $html_dom->getElementsByTagName("title");
            foreach ($q_tmp as $title_tmp) {
                //echo "In  it";
                $title_tmp->textContent = $_name . "_SF36调查表";
            }
            $q_tmp = $html_dom->getElementById("q50");
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