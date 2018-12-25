<?php
	if (isset($_POST['usun'])) {
		$ldapuser = $_SESSION['username'];
		$ldappass = $_SESSION['password'];
		$ldap_con = ldap_connect($ldapnihost) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	
		$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die("Nie udało się jako admin...");
		
		if ($conn) {
			foreach ($_POST['delikwent'] as $key=>$dn) {
				//$konto = $_POST['nazwa_konta'][$key];
				$r = ldap_delete($ldap_con, $dn);
				if($r){
				?>
				<div class="alert alert-success panel_member">
					<strong>Sukces! </strong>Użytkownik o DN '<i><?php echo $dn ?></i>' został usunięty poprawnie.
				</div>
				<?php
				} else{
				?>
				<div class="alert alert-danger panel_member">
					<strong>Błąd: </strong>Użytkownik o DN '<i><?php echo $dn ?></i>' nie został usunięty (<?php ldap_error($conn) ?>).
				</div>
				<?php
				}
			}

			ldap_unbind($conn);
		}
	}
	else {
	?>
			<div class="alert alert-info panel_member">
				<strong>Info: </strong>Niepoprawna akcja, za chwilę nastąpi przekierowanie do strony głównej...
			</div>
			<script>setTimeout(function(){window.location.href="index.php?podstrona=glowna"},2500);</script>

	<?php
}
?>
