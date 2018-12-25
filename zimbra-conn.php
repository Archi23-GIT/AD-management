<?php
$zimbraAdminEmail = getCNofDN($_SESSION['username'])."@".$_SESSION['mailDomain'];
$zimbraAdminPassword = $_SESSION['password'];
$api = \Zimbra\Admin\AdminFactory::instance('https://'.$_SESSION['mailserver'].':7071/service/admin/soap');
$api->auth($zimbraAdminEmail, $zimbraAdminPassword);
?>
