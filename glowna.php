<?php

$ldapuser = $_SESSION['username'];
$ldappass = $_SESSION['password'];
$ldap_con = ldap_connect($_SESSION['ldaphost']) or die("<br>".lang_msg("Connection problem...")."<br>");
ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);

$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die(lang_msg("Failed as admin ..."));

if ($conn) {
	//Wszystkich obiektow
	$search_result = ldap_search($ldap_con, $_SESSION['basedn'], "(objectClass=*)",array("cn"));
	if (($search_result != NULL) && ($search_result != false)) {
		$result_entries = ldap_get_entries($ldap_con, $search_result);
		$all_users = $result_entries['count'];
	}

	//Aktywnych użytkowników
	$search_result = ldap_search($ldap_con, $_SESSION['basedn'], "(&(objectClass=person)(!(objectClass=computer))(!(objectClass=group))(!(userAccountControl:1.2.840.113556.1.4.803:=2)))",array("cn"));
	if (($search_result != NULL) && ($search_result != false)) {
		$result_entries = ldap_get_entries($ldap_con, $search_result);
		$active_person_users = $result_entries['count'];
	}

	//Zablokowanych użytkowników
	$search_result = ldap_search($ldap_con, $_SESSION['basedn'], "(userAccountControl:1.2.840.113556.1.4.803:=2)",array("cn"));
	if (($search_result != NULL) && ($search_result != false)) {
		$result_entries = ldap_get_entries($ldap_con, $search_result);
		$locked_person_users = $result_entries['count'];
	}

	//Z przeterminowaniem użytkowników
	$search_result = ldap_search($ldap_con, $_SESSION['basedn'], "(&(!(accountexpires=9223372036854775807))(!(accountexpires=0)))",array("cn"));
	if (($search_result != NULL) && ($search_result != false)) {
		$result_entries = ldap_get_entries($ldap_con, $search_result);
		$expired_person_users = $result_entries['count'];
	}
	
	
	$search_result = ldap_search($ldap_con, $_SESSION['basedn'], "(objectClass=computer)");
	if (($search_result != NULL) && ($search_result != false)) {
		$result_entries = ldap_get_entries($ldap_con, $search_result);
		$computer_users = $result_entries['count'];
	}

	$search_result = ldap_search($ldap_con, $_SESSION['basedn'], "(objectClass=group)");
	if (($search_result != NULL) && ($search_result != false)) {
		$result_entries = ldap_get_entries($ldap_con, $search_result);
		$group_users = $result_entries['count'];
	}
	
	ldap_unbind($ldap_con);
}

?>
<h3><?php echo lang_msg("Welcome to the Active Directory management support system") ?></h3>
<p><?php echo lang_msg("Select the action from the menu at the top of the page.") ?></p>
<br>
<p><?php echo lang_msg("Active Directory tree statistics:"); ?> <b><i><?php echo $_SESSION['domain']; ?></i></b></p>
<table border=1 cellpadding="10">
<tr>
<td bgcolor="#eeeeee" align="center">
	<img src="images/object.png" width=60>
	<?php 
		echo lang_msg("Objects:");
		echo "<div><b><font size=+2>".$all_users."</font></b></div>";
	?>
</td>
<td bgcolor="#00FF00" align="center">
	<img src="images/person.png" width=50>
	<?php 
		echo lang_msg("Active persons:");
		echo "<div><b><font size=+2>".$active_person_users."</font></b></div>";
	?>
</td>
<td bgcolor="#FFF0000" align="center">
	<img src="images/disabled.png" width=50>
	<?php 
		echo lang_msg("Locked persons:");
		echo "<div><b><font size=+2>".$locked_person_users."</font></b></div>";
	?>
</td>
<td bgcolor="#FFF0000" align="center">
	<img src="images/passexpired.png" width=50>
	<?php 
		echo lang_msg("With expiration date persons:");
		echo "<div><b><font size=+2>".$expired_person_users."</font></b></div>";
	?>
</td>
<td bgcolor="yellow" align="center">
	<img src="images/computer.png" width=50>
	<?php 
		echo lang_msg("Computers:");
		echo "<div><b><font size=+2>".$computer_users."</font></b></div>";
	?>
</td>
<td bgcolor="yellow" align="center">
	<img src="images/group.png" width=50>
	<?php 
		echo lang_msg("Groups:");
		echo "<div><b><font size=+2>".$group_users."</font></b></div>";
	?>
</td>

</tr>
</table>

<p>
</p>

