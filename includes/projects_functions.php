<?php
$sort=$_GET['sort'];
$actioned=$_GET['actioned'];

if ($sort == "") {
$onresult = mysql_query("SELECT * FROM uestatus WHERE status='ongoing'");
} else {
$onresult = mysql_query("SELECT * FROM uestatus WHERE status='ongoing' ORDER BY $sort ASC");
}

if ($sort == "") {
$upresult = mysql_query("SELECT * FROM uestatus WHERE status='uploading'");
} else {
$upresult = mysql_query("SELECT * FROM uestatus WHERE status='uploading' ORDER BY $sort ASC");
}

if ($sort == "") {
$dnresult = mysql_query("SELECT * FROM uestatus WHERE status='done'");
} else {
$dnresult = mysql_query("SELECT * FROM uestatus WHERE status='done' ORDER BY $sort ASC");
}

if ($sort == "") {
$enresult = mysql_query("SELECT * FROM uestatus WHERE status='encoding'");
} else {
$enresult = mysql_query("SELECT * FROM uestatus WHERE status='encoding' ORDER BY $sort ASC");
}

if ($sort == "") {
$lvresult = mysql_query("SELECT * FROM uestatus WHERE status='live'");
} else {
$lvresult = mysql_query("SELECT * FROM uestatus WHERE status='live' ORDER BY $sort ASC");
}

if ($sort == "") {
$claimedresult = mysql_query("SELECT * FROM uestatus WHERE status='claimed'");
} else {
$claimedresult = mysql_query("SELECT * FROM uestatus WHERE status='claimed' ORDER BY $sort ASC");
}
?>