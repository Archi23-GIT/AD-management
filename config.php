<?php
	$ldapnihost = "82.145.72.13";

	$ldaphost = "ldap.zut.edu.pl";

	$wi_cx = "cn=users,dc=wipsad,dc=local";

	//OBSŁUGA ZIMBRA
	// Czy właczyć obsługę sytemu pocztoweo ZIMBRA (domyślnie: false)
	$_SESSION['enableZimbra'] = true;
	// adres serwera Zimbra
	$_SESSION['mailserver'] = "zimbra.wi.zut.edu.pl";
	// domena pocztowa Zimbra
	$_SESSION['mailDomain'] = "wi.zut.edu.pl";

?>
