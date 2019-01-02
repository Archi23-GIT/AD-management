<?php
	if (isset($_POST['dodaj'])) {
		$file = fopen($_FILES['f_uzytkownicy']['tmp_name'], 'r+');
		
		//setup connections
		$ldap_con = ldap_connect($ldaphost) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
		$conn = ldap_bind($ldap_con) or die('<div class="alert alert-danger panel_member">
					  <strong>Błąd: </strong>Logowanie jako anon nie powiodło się (LDAP ZUT).
					  </div>');
		$ldapuser = $_SESSION['username'];
		$ldappass = $_SESSION['password'];
		$ldap_nicon = ldap_connect($ldapnihost) or die("Problem z połaczeniem...");
		ldap_set_option($ldap_nicon, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_nicon, LDAP_OPT_REFERRALS, 0);
		$niconn = ldap_bind($ldap_nicon, $ldapuser, $ldappass) or die('<div class="alert alert-danger panel_member">
					  <strong>Błąd: </strong>Logowanie jako admin nie powiodło się (NI WIZUT).
					  </div>');
		
		while (($line = fgetcsv($file)) !== FALSE) {

			if ($conn && $niconn) {
				$s = ldap_search($ldap_con,"cn=users,cn=accounts,dc=zut,dc=edu,dc=pl","(uid=".$line[0].")");
				//print_r($s);
				$info = ldap_get_entries($ldap_con, $s);
				//print_r($info);
				$konto = $line[0];
				if ($info['count'] >= 1) {
					$imie = $info[0]['givenname'][0];
					$nazwisko = $info[0]['sn'][0];
					$email = $info[0]['mailalternateaddress'][0];
					$konto = $info[0]['uid'][0];
					$grupa = "Pulpitowi Stud";
					
					$s1 = ldap_search($ldap_nicon, $wi_cx, "(uidnumber=*)", array("uidnumber"));
					$result = ldap_get_entries($ldap_nicon, $s1);
					$count = $result['count'];
					rsort($result);
					$biguid = $result[0]['uidnumber'][0] + 1;
					
					$wilogin = "cn=".$konto.",cn=users,dc=wipsad,dc=local";
					//echo $imie . " " . $nazwisko . " " . $email . " " .$konto;
					$entry['objectClass'][0] = "top";
					$entry['objectClass'][1] = "person";
					$entry['objectClass'][2] = "organizationalPerson";
					$entry['objectClass'][3] = "user";
					$entry['cn'] = $konto;
					$entry['sn'] = $nazwisko;
					$entry['name'] = $konto;
					$entry['homeDirectory'] = "\\\\beta-d\\users$\\".$konto;
					$entry['homeDrive'] = "I:";
					$entry['sAMAccountName'] = $konto;
					$entry['userAccountControl'] = "66050";
					$entry['displayName'] = $nazwisko." ".$imie;
					$entry['givenName'] = $imie;
					$entry['userPrincipalName'] = $konto;
					$entry['uid'] = $konto;
					$entry['uidNumber'] = $biguid;
					$entry['gidNumber'] = "100";
					$entry['unixHomeDirectory'] = "/home/".$konto;
					$entry['loginShell'] = "/bin/bash";
					$entry['msSFU30Name'] = $konto;
					$entry['msSFU30NisDomain'] = "WIPSAD";
					$entry['mail'] = $email;
					
					// proba dodania usera
					if ($niconn) {
						// sprawdz czy juz istnieje
						$s = ldap_search($ldap_nicon,$wi_cx,"(uid=".$konto.")");
						$info = ldap_get_entries($ldap_nicon, $s);
						if ($info['count'] >= 1){		
							// istnieje - anuluj dodawanie
		?>					
							<div class="alert alert-warning panel_member">
								<strong>Ostrzeżenie: </strong>Użytkownik <?php echo $konto; ?> istnieje i nie może zostać dodany ponownie (NI WIZUT).
							</div>
		<?php
						}else{
							$r = ldap_add($ldap_nicon, $wilogin, $entry);
							//$r = ldap_add($ldap_con, $wilogin, $entry);
							if ($r) {
								//proba dodania do grupy studentow
								$group_name = "cn=Pulpitowi Stud,cn=users,dc=wipsad,dc=local";
								$group_info['member'] = $wilogin;
								$rg = ldap_mod_add($ldap_nicon, $group_name, $group_info);
								if ($rg){
		?>
									<div class="alert alert-success panel_member">
										<strong>Sukces!</strong> Dodano nowego użytkownika (<?php echo $konto; ?>) (NI WIZUT).
									</div>
		<?php
								} else {
		?>
									<div class="alert alert-info panel_member">
										<strong>Sukces...</strong> Dodano nowego użytkownika (<?php echo $konto; ?>), jednak wystąpił błąd dodawania do grupy <?php echo $grupa ?> (NI WIZUT).
									</div>
		<?php
								}
							} else{
		?>
								<div class="alert alert-danger panel_member">
									<strong>Błąd:</strong> Nie udało się dodać użytkownika (<?php echo $konto; ?>) (NI WIZUT).
								</div>
		<?php
							}
						}
					} 
				} else{
					echo '<div class="alert alert-danger panel_member">
					  <strong>Błąd: </strong> Nie udało się dodać użytkownika ' . $konto . ' (NI WIZUT). Prawdopodobnie brakuje wpisu w (LDAP ZUT).
					  </div>';
					continue;
				}
				
			}				
		}
		fclose($file);
		ldap_unbind($ldap_con);
		ldap_unbind($ldap_nicon);
	}
	else{
?>
		<h1>Dodawanie kont użytkowników z pliku CSV</h1>

		<form action="index.php?podstrona=dodaj_plik" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
			<table>
				<tr>
					<th>Plik:</th>
					<td><input type="file" name="f_uzytkownicy"></td>
				</tr>
				<tr>
					<td rowspan="2"><button type="submit" name="dodaj">Dodaj!</button></td>
				</tr>
			</table>
		</form>
<?php
	}
?>
