<?php
	if (isset($_POST['blokuj'])) {
		$wi_cx = $_SESSION['basedn'];
		$ldapuser = $_SESSION['username'];
		$ldappass = $_SESSION['password'];
		$ldap_con = ldap_connect($_SESSION['ldaphost']) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	
		$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die("Nie udało się jako admin...");
		
		if ($conn) {
			foreach ($_POST['delikwent'] as $key=>$dn) {
				$sr = ldap_read($ldap_con, $dn, "(objectclass=*)");
				$info = ldap_get_entries($ldap_con, $sr);
				//print_r($info);
				if($info['count'] >=1){
					$curr_date = date_create('now');
					$blockdate_timestamp = $curr_date->getTimestamp();
					$blockdate_timestamp += 11644473600;
					$blockdate_timestamp *= 10000000;
					$e_newdata=array();
					$e_newdata['accountexpires'][0]=$blockdate_timestamp;
					$e_newdata['userAccountControl'][0]=66050;
					// Zmiana w domienie windows
					$r = ldap_modify($ldap_con, $dn, $e_newdata);
					$R_WIN = $r;
					
					
					if($r){
					?>
						<div class="alert alert-success panel_member">
							<strong>Success! </strong>The user <i>dn: '<?php echo strtoupper($dn) ?></i>' is locked in domain <?php echo strtoupper($_SESSION['domain']) ?>.
						</div>
					<?php
					} else{
					?>
						<div class="alert alert-danger panel_member">
							<strong>Error: </strong>The user <i>dn: '<?php echo $dn ?></i>' has not been locked.
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

			ldap_unbind($ldap_con);
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
