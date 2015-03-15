<?php

$ftpServer = "ftp.microsoft.com";
$ftpUser = "anonymous";
$ftpPass = "me@myhost.com";

set_time_limit(160);

$conn = @ftp_connect($ftpServer)
or die("Couldn't connect to FTP server");

$login = @ftp_login($conn, $ftpUser, $ftpPass)
or die("Login credentials were rejected");

$workingDir = ftp_pwd($conn);
echo "You are in the directory: $workingDir";

ftp_quit($conn);

?>