<?php
	// adres servera AD
    $ldapnihost = "ad.com.pl";

    // adres servera LDAP do pobieranie nazw
	$ldaphost = "ldap.com.pl";

	// podstawowy kontener użytkowników w AD
	$wi_cx = "cn=users,dc=wipsad,dc=local";

	//OBSĹ�UGA ZIMBRA
	// Czy wĹ‚aczyÄ‡ obsĹ‚ugÄ™ sytemu pocztoweo ZIMBRA (domyĹ›lnie: false)
	$_SESSION['enableZimbra'] = true;
	// adres serwera Zimbra
	$_SESSION['mailserver'] = "zimbra.wi.zut.edu.pl";
	// domena pocztowa Zimbra
	$_SESSION['mailDomain'] = "wi.zut.edu.pl";

?>
