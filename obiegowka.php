<?php
	if (isset($_POST['obiegowka'])) {
		$wi_cx = "cn=users,dc=wipsad,dc=local";
		$ldapuser = $_SESSION['username'];
		$ldappass = $_SESSION['password'];
		$ldap_con = ldap_connect($ldapnihost) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	
		$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die("Nie udało się jako admin...");
		
		if ($conn) {
			foreach ($_POST['delikwent'] as $key=>$dn) {
				//$s = ldap_search($ldap_con,$wi_cx,"(dn=".$dn.")");
				$sr = ldap_read($ldap_con, $dn, "(objectclass=*)");
				$info = ldap_get_entries($ldap_con, $sr);
				if($info['count'] >=1){
					//$entry = $info[0];
					// przygotuj date blokady (dzis + 30 dni)
					$curr_date = date_create('now');
					date_add($curr_date, date_interval_create_from_date_string('30 days'));
					$blockdate_timestamp = $curr_date->getTimestamp();
					// dodaj sekundy od 1601-01-01 (win epoch) do 1970-01-01 (unix epoch)
					$blockdate_timestamp += 11644473600;
					// konwertuj do 100-nanosekundowych interwalow
					$blockdate_timestamp *= 10000000;

					$expiry_newdata=array();
					$expiry_newdata['accountexpires'][0]=$blockdate_timestamp;
					//zaktualizuj date blokady
					$r = ldap_modify($ldap_con, $dn, $expiry_newdata);
					if($r){
					?>
						<div class="alert alert-success panel_member">
							<strong>Sukces! </strong>Użytkownik o DN '<i><?php echo $dn ?></i>' został oznaczony do zablokowania za 30 dni (NI WIZUT).
						</div>
					<?php
					} else{
					?>
						<div class="alert alert-danger panel_member">
							<strong>Błąd: </strong>Użytkownik o DN '<i><?php echo $dn ?></i>' nie mógł zostać oznaczony do zablokowania (być może już nie istnieje) (NI WIZUT).
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
