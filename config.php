<?php
	//Set of language
	//$_SESSION['lang'] = "en_EN";
	$_SESSION['lang'] = "pl_PL";
	
	//Domain parametrs
	$_SESSION['domain'] = "WIPSAD";
	
	$ldapnihost = "82.145.72.13";
	$_SESSION['ldaphost'] = "82.145.72.13";
	
	$ldaphost = "ldap.zut.edu.pl";
	$_SESSION['ldaphost-get'] = "ldap.zut.edu.pl";
	
	$wi_cx = "dc=wipsad,dc=local";
	$_SESSION['basedn'] = "dc=wipsad,dc=local";
	$_SESSION['admin-basedn'] = "cn=users,dc=wipsad,dc=local";
	
	$_SESSION['basedn-get'] = "cn=users,cn=accounts,dc=zut,dc=edu,dc=pl";
	
	//Filter only users not disabled and not computer and not group object
	// used on lista.php
	$_SESSION['filter'] = "(&(objectClass=person)(!(objectClass=computer))(!(objectClass=group))(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
	$_SESSION['attr'] = array("*");
	
	
	//OBSŁUGA ZIMBRA
	// Czy właczyć obsługę sytemu pocztoweo ZIMBRA (domyślnie: false)
	$_SESSION['enableZimbra'] = false;
	// adres serwera Zimbra
	$_SESSION['mailserver'] = "zimbra.wi.zut.edu.pl";
	// domena pocztowa Zimbra
	$_SESSION['mailDomain'] = "wi.zut.edu.pl";

?>
