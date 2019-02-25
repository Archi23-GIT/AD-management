<?php
	if (isset($_POST['sprawdz'])) {
		$konto = $_POST['konto'];
		$grupa = $_POST['grupa'];
		
		if($grupa == "Pulpitowi Stud"){
			$ldapuser = $_SESSION['username'];
			$ldappass = $_SESSION['password'];
			$ldap_con = ldap_connect($_SESSION['ldaphost-get']) or die('<div class="alert alert-danger panel_member">
														 <strong>Błąd: </strong>Nieudane połączenie z (LDAP ZUT).
													   </div>');
			ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
			$conn = ldap_bind($ldap_con) or die('<div class="alert alert-danger panel_member">
													<strong>Błąd: </strong>Nieudane połączenie z (LDAP ZUT).
												</div>');
			
			if($conn) {
				// szukanie danych w ldap zut
				$s = ldap_search($ldap_con,$_SESSION['basedn-get'],"(uid=".$konto.")");
				$info = ldap_get_entries($ldap_con, $s);
				if ($info['count'] < 1) {
					echo '<div class="alert alert-danger panel_member">
						<strong>Błąd: </strong>Nie odnazielono użytkownika o loginie ' . $konto . ' (LDAP ZUT).
						</div>
					    <form action="index.php?podstrona=dodaj_jeden" method="POST">
							<td rowspan="2"><button class="btn btn-warning" type="submit" name= "anuluj"/>Anuluj</button></td>
						</form>';
					die();
				}
				$imie = $info[0]['givenname'][0];
				$nazwisko = $info[0]['sn'][0];
				$email = $info[0]['mailalternateaddress'][0];
				$konto = $info[0]['uid'][0];
				ldap_unbind( $ldap_con );
			}else {
				echo '<div class="alert alert-danger panel_member">
				  <strong>Błąd: </strong>Brak połączenia z (LDAP ZUT).
				  </div>
				  <form action="index.php?podstrona=dodaj_jeden" method="POST">
					<td rowspan="2"><button class="btn btn-warning" type="submit" name= "anuluj"/>Anuluj</button></td>
				   </form>';
				die();
			}
		} else{
			echo '<div class="alert alert-default panel_member">
					<strong>Info: </strong>Potwierdź zgodność danych przed dodaniem konta (NI WIZUT).
				  </div>';
			$nazwisko = $_POST['nazwisko'];
			$imie = $_POST['imie'];
			$email = $_POST['email'];
			$konto = $_POST['konto'];
			$grupa = $_POST['grupa'];
		}

			?>
			<h1>Dodawanie konta użytkownika</h1>
			<form action="index.php?podstrona=dodaj_jeden" method="POST">
				<table>
					<tr>
						<th>Nazwisko:</th>
						<td><input type="text" readonly="true" name="nazwisko" placeholder="Nazwisko" value=<?php echo $nazwisko ?> required></td>
					</tr>
					<tr>
						<th>Imię:</th>
						<td><input type="text" readonly="true" name="imie" placeholder="Imię" value=<?php echo $imie ?> required></td>
					</tr>
					<tr>
						<th>Adres e-mail:</th>
						<td><input type="text" readonly="true" name="email" placeholder="Adres e-mail" value=<?php echo $email ?> required></td>
					</tr>
					<tr>
						<th>Nazwa konta:</th>
						<td><input type="text" readonly="true" name="konto" placeholder="Nazwa konta" value=<?php echo $konto ?> required ></td>
					</tr>
					<tr>
						<th>Grupa:</th>
						<td><input type="radio" name="grupa" id="gidStudent" checked="checked" value="Pulpitowi Stud">Student<input type="radio" disabled name="grupa" id="gidDydaktyk" value="Pulpitowi Dyda">Dydaktyk <!-- <input type="radio" name="grupa" value="brak">Brak --></td>
					</tr>
					<tr>
						<td rowspan="2"><button class="btn btn-success" type="submit" name="zapisz">Zapisz!</button></td>
						<td rowspan="2"><button class="btn btn-inactive" type="submit" disabled name="inactive">Sprawdź</button></td>
						<td rowspan="2"><button class="btn btn-warning" type="submit" name= "anuluj"/>Anuluj</button></td>
					</tr>
				</table>
			</form>
<?php
	}
	else if (isset($_POST['zapisz'])) {
		$nazwisko = $_POST['nazwisko'];
		$imie = $_POST['imie'];
		$email = $_POST['email'];
		$konto = $_POST['konto'];
		$grupa = $_POST['grupa'];
		$wilogin = "cn=".$konto.",".$_SESSION['basedn'];
		$wi_cx = $_SESSION['basedn'];
?>		
		<h3>Dodawanie konta użytkownika</h3>
		
		<div class="card">
			<div class="card-header" style="text-align: center;">
				Weryfikacja w LDAP ZUT.
			</div>
			<div class="card-text">
<?php
			$ldapuser = $_SESSION['username'];
			$ldappass = $_SESSION['password'];
			$ldap_nicon = ldap_connect($_SESSION['ldaphost']) or die("Problem z połaczeniem...");
			ldap_set_option($ldap_nicon, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap_nicon, LDAP_OPT_REFERRALS, 0);
			$niconn = ldap_bind($ldap_nicon, $ldapuser, $ldappass) or die
				('<div class="alert alert-danger panel_member">
				  <strong>Błąd: </strong>Logowanie jako admin nie powiodło się (NI WIZUT).
				  </div>');
				  
			if($grupa == "Pulpitowi Stud"){
				$ldapuser = $_SESSION['username'];
				$ldappass = $_SESSION['password'];
				$ldap_con = ldap_connect($_SESSION['ldaphost-get']) or die("<br>Problem z połaczeniem...(LDAP ZUT)<br>");
				ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
				$conn = ldap_bind($ldap_con) or die("Nie udało się jako anon(LDAP ZUT).");
				
				
				
				
				//$ldapuser = "cn=administrator,cn=users,dc=wipsad,dc=local";
				if($conn) {
					// dodawanie studenta
					// szukanie danych w ldap zut
					$s = ldap_search($ldap_con,$_SESSION['basedn-get'],"(uid=".$konto.")");
					$info = ldap_get_entries($ldap_con, $s);
					if ($info['count'] < 1) {
						echo '<div class="alert alert-danger panel_member">
							<strong>Błąd: </strong>Nie odnazielono użytkownika o loginie ' . $konto . ' (LDAP ZUT).
							</div>';
						die();
					}
					$imie = $info[0]['givenname'][0];
					$nazwisko = $info[0]['sn'][0];
					$email = $info[0]['mailalternateaddress'][0];
					$konto = $info[0]['uid'][0];
					//ldap_unbind( $ldap_con );
				}
				
			} else{
				// zapisywanie danych podanych bezposrednio w formualarzu
					$imie = $_POST['imie'];
					$email = $_POST['email'];
					$konto = $_POST['konto'];
					$grupa = $_POST['grupa'];
					$nazwisko = $_POST['nazwisko'];
				}
				
				
				
				// przygotowywanie struktury
				$s1 = ldap_search($_SESSION['ldaphost'], $_SESSION['basedn'], "(uidnumber=*)", array("uidnumber"));
				$result = ldap_get_entries($ldap_nicon, $s1);
				$count = $result['count'];
				rsort($result);
				$biguid = $result[0]['uidnumber'][0] + 1;
				//ldap_unbind($ldap_con);
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
	?>
				<div class="alert alert-success panel_member">
					<strong>Sukces!</strong> Odszukano dane użytkownika <?php echo $konto; ?> (LDAP ZUT).
				</div>
				<div class="panel_member">
					<table class="table" id="entry_podglad">
						<tr>
							<th>objectClass[0]</th>
							<td><?php echo $entry['objectClass'][0]; ?></td>
						</tr>
						<tr>
							<th>objectClass[1]</th>
							<td><?php echo $entry['objectClass'][1]; ?></td>
						</tr>
						<tr>
							<th>objectClass[2]</th>
							<td><?php echo $entry['objectClass'][2]; ?></td>
						</tr>
						<tr>
							<th>objectClass[3]</th>
							<td><?php echo $entry['objectClass'][3]; ?></td>
						</tr>
						<tr>
							<th>cn</th>
							<td><?php echo $entry['cn'] ; ?></td>
						</tr>
						<tr>
							<th>sn</th>
							<td><?php echo $entry['sn']; ?></td>
						</tr>
						<tr>
							<th>name</th>
							<td><?php echo $entry['name']; ?></td>
						</tr>
						<tr>
							<th>homeDirectory</th>
							<td><?php echo $entry['homeDirectory']; ?></td>
						</tr>
						<tr>
							<th>homeDrive</th>
							<td><?php echo $entry['homeDrive']; ?></td>
						</tr>
						<tr>
							<th>sAMAccountName</th>
							<td><?php echo $entry['sAMAccountName']; ?></td>
						</tr>
						<tr>
							<th>userAccountControl</th>
							<td><?php echo $entry['userAccountControl']; ?></td>
						</tr>
						<tr>
							<th>displayName</th>
							<td><?php echo $entry['displayName']; ?></td>
						</tr>
						<tr>
							<th>givenName</th>
							<td><?php echo $entry['givenName']; ?></td>
						</tr>
						<tr>
							<th>userPrincipalName</th>
							<td><?php echo $entry['userPrincipalName']; ?></td>
						</tr>
						<tr>
							<th>uid</th>
							<td><?php echo $entry['uid']; ?></td>
						</tr>
						<tr>
							<th>uidNumber</th>
							<td><?php echo $entry['uidNumber']; ?></td>
						</tr>
						<tr>
							<th>gidNumber</th>
							<td><?php echo $entry['gidNumber']; ?></td>
						</tr>
						<tr>
							<th>unixHomeDirectory</th>
							<td><?php echo $entry['unixHomeDirectory']; ?></td>
						</tr>
						<tr>
							<th>loginShell</th>
							<td><?php echo $entry['loginShell']; ?></td>
						</tr>
						<tr>
							<th>msSFU30Name</th>
							<td><?php echo $entry['msSFU30Name']; ?></td>
						</tr>
						<tr>
							<th>msSFU30NisDomain</th>
							<td><?php echo $entry['msSFU30NisDomain']; ?></td>
						</tr>
						<tr>
							<th>mail</th>
							<td><?php echo $entry['mail']; ?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header" style="text-align: center;">
				Dodawanie do domeny.
			</div>
			<div class="card-text">
<?php
			ldap_unbind($ldap_con);

			$ldapuser = $_SESSION['username'];
			$ldappass = $_SESSION['password'];
			$ldap_nicon = ldap_connect($_SESSION['ldaphost']) or die("Problem z połaczeniem...");
			ldap_set_option($ldap_nicon, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap_nicon, LDAP_OPT_REFERRALS, 0);
			$niconn = ldap_bind($ldap_nicon, $ldapuser, $ldappass) or die('<div class="alert alert-danger panel_member">
					  <strong>Błąd: </strong>Logowanie jako admin nie powiodło się (NI WIZUT).
					  </div>');
			if ($niconn) {
				// sprawdz czy juz istnieje
				$s = ldap_search($ldap_nicon,$wi_cx,"(uid=".$konto.")");
				$info = ldap_get_entries($ldap_nicon, $s);
				if ($info['count'] >= 1){
					// istnieje - anuluj dodawanie
?>					
				<div class="alert alert-danger panel_member">
					<strong>Błąd: </strong>Użytkownik <?php echo $konto ?> istnieje i nie może zostać dodany ponownie (NI WIZUT).
				</div>
<?php
				}else{
					$r = ldap_add($ldap_nicon, $wilogin, $entry);
					//$r = ldap_add($ldap_con, $wilogin, $entry);
					if ($r) {
?>
						<div class="alert alert-success panel_member">
							<strong>Sukces!</strong> Dodano nowego użytkownika (<?php echo $wilogin; ?>) (NI WIZUT) .
						</div>
<?php
						// Dodanie do grupy
						$group_name = "cn=".$grupa.",".$_SESSION['basedn'];
						$group_info['member'] = $wilogin;
						$r = ldap_mod_add($ldap_nicon, $group_name, $group_info);
						//$r = ldap_mod_add($ldap_con, $group_name, $group_info);
						if ($r) {
?>
							<div class="alert alert-success panel_member">
								<strong>Sukces!</strong> Użytkownik został dodany do grupy (<?php echo $grupa; ?>).
							</div>
<?php						
						}
						else {
?>
							<div class="alert alert-warning panel_member">
								<strong>Ostrzeżenie:</strong> Nie udało się dodać użytkownika do grupy <?php echo $grupa; ?> (grupa może nie istnieć).
							</div>
<?php						
						}
					}
					else {
?>
						<div class="alert alert-danger panel_member">
							<strong>Błąd:</strong> Nie udało się dodać użytkownika <?php echo $wilogin; ?>(NI WIZUT).
						</div>
<?php						
					}
				}
		}?>
		</div>
<?php
		ldap_unbind($ldap_con);
		ldap_unbind($ldap_nicon);
}
else{
	unset($konto, $imie, $nazwisko, $grupa, $email);
?>
	<h1>Dodawanie konta użytkownika</h1>

	<form action="index.php?podstrona=dodaj_jeden" method="POST">
		<table>
			<tr>
				<th>Nazwisko:</th>
				<td><input type="text" disabled="disabled" name="nazwisko" placeholder="Nazwisko" ></td>
			</tr>
			<tr>
				<th>Imię:</th>
				<td><input type="text" disabled="disabled" name="imie" placeholder="Imię" ></td>
			</tr>
			<tr>
				<th>Adres e-mail:</th>
				<td><input type="text" disabled="disabled" name="email" placeholder="Adres e-mail" ></td>
			</tr>
			<tr>
				<th>Nazwa konta:</th>
				<td><input type="text" name="konto" placeholder="Nazwa konta" required></td>
			</tr>
			<tr>
				<th>Grupa:</th>
				<td><input type="radio" name="grupa" id="gidStudent" checked="checked" value="Pulpitowi Stud">Pulpitowi Stud<br>
					<input type="radio" name="grupa" id="gidDydaktyk" value="Pulpitowi Dyda">Pulpitowi Dyda<br>
					<input type="radio" name="grupa" id="gidMailonly" value="MailOnly">MailOnly
				</td>
			</tr>
			<tr>
				<td rowspan="2"><button class="btn btn-success" type="submit" id="butZapisz" disabled name="zapisz">Zapisz!</button></td>
				<td rowspan="2"><button class="btn btn-primary" type="submit" id="butSprawdz" name="sprawdz">Sprawdź</button></td>
				<td rowspan="2"><button class="btn btn-warning" type="submit" id="butAnuluj" name= "anuluj"/>Anuluj</button></td>
			</tr>
		</table>
	</form>
	<script>
		var $inputGrupa = $('input[name=grupa]');
		var $inputImie = $('input[name=imie]');
		var $inputNazwisko = $('input[name=nazwisko]');
		var $inputEmail = $('input[name=email]');
		var $buttonZapisz = $('#butZapisz');
		var $buttonAnuluj = $('#butAnuluj');
		var $buttonSprawdz = $('#butSprawdz');
		
		$inputGrupa.change(function () {
			$currGrupa = $('input[name=grupa]:checked').val();
			if ($currGrupa == 'Pulpitowi Stud') {
				$inputImie.attr('disabled', 'disabled');
				$inputNazwisko.attr('disabled', 'disabled');
				$inputEmail.attr('disabled', 'disabled');
				//buttony
				//$buttonSprawdz.removeAttr('disabled');
				//$buttonSprawdz.removeClass("btn-inactive");
				//$buttonSprawdz.addClass("btn-primary");
				$buttonZapisz.attr('disabled', 'disabled');
				$buttonZapisz.removeClass("btn-success");
				$buttonZapisz.addClass("btn-inactive");
			} else {
				$inputImie.removeAttr('disabled');
				$inputNazwisko.removeAttr('disabled');
				$inputEmail.removeAttr('disabled');
				//buttony
				$buttonZapisz.removeAttr('disabled');
				$buttonZapisz.removeClass("btn-inactive");
				$buttonZapisz.addClass("btn-success");
				//$buttonSprawdz.attr('disabled', 'disabled');
				//$buttonSprawdz.removeClass("btn-primary");
				//$buttonSprawdz.addClass("btn-inactive");
			}
		}).trigger('change');
		
	</script>
<?php
}
?>
