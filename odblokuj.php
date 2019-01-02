<?php
	if (isset($_POST['odblokuj'])) {
		$wi_cx = "cn=users,dc=wipsad,dc=local";
		$ldapuser = $_SESSION['username'];
		$ldappass = $_SESSION['password'];
		$ldap_con = ldap_connect($ldapnihost) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	
		$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die("Nie udało się jako admin...");
		
		if ($conn) {
			foreach ($_POST['delikwent'] as $key=>$dn) {
				$sr = ldap_read($ldap_con, $dn, "(objectclass=*)");
				$info = ldap_get_entries($ldap_con, $sr);
				//print_r($info);
				if($info['count'] >=1){
					$e_newdata=array();
					$e_newdata['accountexpires'][0]=9223372036854775807;
					$e_newdata['userAccountControl'][0]=66048;
					$r = ldap_modify($ldap_con, $dn, $e_newdata);
					if($r){
					?>
						<div class="alert alert-success panel_member">
							<strong>Sukces! </strong>Użytkownik o DN '<i><?php echo $dn ?></i>' został odblokowany (NI WIZUT).
						</div>
					<?php
					} else{
					?>
						<div class="alert alert-danger panel_member">
							<strong>Błąd: </strong>Użytkownik o DN '<i><?php echo $dn ?></i>' nie mógł zostać odblokowany (NI WIZUT).
						</div>
					<?php
					}
				} else{
				?>
					<div class="alert alert-danger panel_member">
							<strong>Błąd: </strong>Użytkownik o DN '<i><?php echo $dn ?></i>' nie został odnaleziony w (NI WIZUT).
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
