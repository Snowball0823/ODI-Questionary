<pre>
<?php
require_once("db_config.php");
$mysqli = new mysqli($mysql_server_name, $mysql_username, $mysql_password, $mysql_database);
//设置编码
$mysqli->set_charset("utf8"); //或者 $mysqli->query("set names 'utf8'")
//面向对象的昂视屏蔽了连接产生的错误，需要通过函数来判断
if (mysqli_connect_error()) {
    echo mysqli_connect_error() + '\n';
} else {
    echo "连接成功";
}
//设置编码
//$mysqli->set_charset("utf8"); //或者 $mysqli->query("set names 'utf8'")

$_name = $_POST["name"];
$_sex = $_POST["sex"];
$_id = $_POST["id"];
$_radios = json_decode($_POST["radios"]);
$_numbers = json_decode($_POST["numbers"]);
$sql = "SELECT * FROM " . $mysql_table;
$result = $mysqli->query($sql);
echo $result;
//echo $_numbers;
/*foreach ($_numbers as $tmpValue) {
    echo $tmpValue;
}*/
/*array_multisort(
    $cars[0],
    SORT_ASC,
    SORT_STRING,
    $cars[1],
    SORT_DESC
);*/
//echo $cars;
//关闭连接
/*$sql = "select * from member";
$result = $mysqli->query($sql);
if ($result === false) { //执行失败
    echo $mysqli->error;
    echo $mysqli->errno;
}
//行数
echo "字段数: ", $result->num_rows, "</br>";
//列数 字段数
echo "字段信息: ", $result->field_count, "</br>";
//获取字段信息
$field_info_arr = $result->fetch_fields();
//移动记录指针
//$result->data_seek(1);//0 为重置指针到起始
//获取数据
//while($row = $result->fetch_assoc()){(也可以)
$i = 0;
foreach ($result as $key => $row) {
    echo $row['name'], "</br>";
    echo $row['birthaddr'], "</br>";
    print_r($row);
    $info[$i] = $row;
    $i++;
}
print_r($result);
print_r($info);*/
//array_multisort();
//也可一次性获取所有数据
/*$result->data_seek(0);//如果前面有移动指针则需重置
$data = $result->fetch_array();
print_r($data);*/
/*$mysqli->close();
echo $_SERVER['PHP_SELF'];
echo "<br>";
echo $_SERVER['SERVER_NAME'];
echo "<br>";
echo $_SERVER['HTTP_HOST'];
echo "<br>";
echo $_SERVER['HTTP_REFERER'];
echo "<br>";
echo $_SERVER['HTTP_USER_AGENT'];
echo "<br>";
echo $_SERVER['SCRIPT_NAME'];*/
/*$data[] = array('volume' => 67, 'edition' => 2);
$data[] = array('volume' => 86, 'edition' => 8);
$data[] = array('volume' => 85, 'edition' => 6);
$data[] = array('volume' => 98, 'edition' => 2);
$data[] = array('volume' => 86, 'edition' => 6);
$data[] = array('volume' => 67, 'edition' => 7);
foreach ($data as $key => $row) {
    $volume[$key]  = $row['volume'];
    $edition[$key] = $row['edition'];
}
array_multisort($volume, SORT_DESC);
print_r($volume);
$i=0;
foreach($volume as $data[$i]['volume']){
    $i++;
};
 print_r($data);*/
?>
< /pre>                         