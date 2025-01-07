<?php
// php & Oracle DB connection file
$user = "CarRental"; //oracle username
$pass = "123456"; //Oracle password
$host = "localhost:1521/xe"; //server name or ip address
$dbconn = oci_connect($user, $pass, $host);
if (!$dbconn) {
$e = oci_error();
trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
} else {
echo "ORACLE DATABASE CONNECTED SUCCESSFULLY!!!<br>"; //you can remove this
}
?>