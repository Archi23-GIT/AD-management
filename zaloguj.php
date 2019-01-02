<?php
	if (isset($_POST['logmein']) && !empty($_POST['username']) && !empty($_POST['password'])) {
		$ldapuser = "cn=".$_POST['username'].",cn=users,dc=wipsad,dc=local";
		$ldappass = $_POST['password'];
		$ldap_con = ldap_connect($ldapnihost) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($ldap_con,LDAP_OPT_REFERRALS,0);
		$conn = ldap_bind($ldap_con,$ldapuser,$ldappass);

		if ($conn) {
			$_SESSION['valid'] = true;
			$_SESSION['timeout'] = time();
			$_SESSION['username'] = $ldapuser;
			$_SESSION['password'] = $ldappass;
			
			ldap_unbind($ldap_con);
			header('Location: index.php?podstrona=glowna');
		 }
		 else {
			$_SESSION['login_error'] = "Błąd!";
			header('Location: index.php?podstrona=zaloguj');
		 }
	}
	else {
?>
		<h1>Zaloguj się!</h1>

		<form action="index.php?podstrona=zaloguj" method="POST">
			<table>
				<tr>
					<th>Login:</th>
					<td><input type="text" name="username" placeholder="Login" required autofocus></td>
				</tr>
				<tr>
					<th>Hasło:</th>
					<td><input type="password" name="password" placeholder="Hasło" required></td>
				</tr>
				<tr>
					<td rowspan="2"><button class="btn btn-success" type="submit" name="logmein">Zaloguj!</button></td>
				</tr>
				<?php
					if (isset($_SESSION['login_error'])) {
				?>
						<tr>
							<td rowspan="2">Błąd logowania! Wprowadź popawne dane!</td>
						</tr>
				<?php
						unset($_SESSION['login_error']);
					}
				?>
			</table>
		</form>
<?php
	}
?>
