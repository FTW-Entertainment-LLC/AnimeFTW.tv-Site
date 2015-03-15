<?php

include("../includes/classes/config.v2.class.php");
include("../includes/classes/account.v2.class.php");

$A = new Account();
$A->removeLoginSessions(0);