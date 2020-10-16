<?php
$sort=$_GET['sort'];
$actioned=$_GET['actioned'];

if ($sort == "") {
$onresult = mysqli_query("SELECT * FROM uestatus WHERE status='ongoing'");
} else {
$onresult = mysqli_query("SELECT * FROM uestatus WHERE status='ongoing' ORDER BY $sort ASC");
}

if ($sort == "") {
$upresult = mysqli_query("SELECT * FROM uestatus WHERE status='uploading'");
} else {
$upresult = mysqli_query("SELECT * FROM uestatus WHERE status='uploading' ORDER BY $sort ASC");
}

if ($sort == "") {
$dnresult = mysqli_query("SELECT * FROM uestatus WHERE status='done'");
} else {
$dnresult = mysqli_query("SELECT * FROM uestatus WHERE status='done' ORDER BY $sort ASC");
}

if ($sort == "") {
$enresult = mysqli_query("SELECT * FROM uestatus WHERE status='encoding'");
} else {
$enresult = mysqli_query("SELECT * FROM uestatus WHERE status='encoding' ORDER BY $sort ASC");
}

if ($sort == "") {
$lvresult = mysqli_query("SELECT * FROM uestatus WHERE status='live'");
} else {
$lvresult = mysqli_query("SELECT * FROM uestatus WHERE status='live' ORDER BY $sort ASC");
}

if ($sort == "") {
$claimedresult = mysqli_query("SELECT * FROM uestatus WHERE status='claimed'");
} else {
$claimedresult = mysqli_query("SELECT * FROM uestatus WHERE status='claimed' ORDER BY $sort ASC");
}
?>