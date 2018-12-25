<?php

	if (isset($_POST['usun'])) {
		$ldapuser = $_SESSION['username'];
		$ldappass = $_SESSION['password'];
		$ldap_con = ldap_connect($ldapnihost) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	
		$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die("Nie udało się jako admin...");

		if ($conn) {
			foreach ($_POST['delikwent'] as $dn) {
				ldap_delete($ldap_con, $dn);
			}

			ldap_unbind($conn);
		}
	}
	else {
		$ldapuser = $_SESSION['username'];
		$ldappass = $_SESSION['password'];
		$ldap_con = ldap_connect($ldapnihost) or die("<br>Problem z połaczeniem...<br>");
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);

		$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die("Nie udało się jako admin...");
?>

		<h3>Lista użytkowników domeny</h3>

<?php
		if ($conn) {
			// Obsługa systemu ZIMBRA
			if ($_SESSION['enableZimbra']) {include 'zimbra-conn.php'; }
			
			$search_result = ldap_list($ldap_con, "cn=users,dc=wipsad,dc=local", "(uid=*)");
			if (($search_result != NULL) && ($search_result != false)) {
				$result_entries = ldap_get_entries($ldap_con, $search_result);
											
				//check and convert dates
				$curr_date = date_create('now');
				date_add($curr_date, date_interval_create_from_date_string('30 days'));
				//echo date_format($curr_date, 'Y-m-d');
				$blockdate_unixtimestamp = $curr_date->getTimestamp();
				// dodaj sekundy od 1601-01-01 (win epoch) do 1970-01-01 (unix epoch)
				$blockdate_unixtimestamp += 11644560000;
				// konwertuj do 100-nanosekundowych interwalow
				$blockdate_unixtimestamp *= 10000000;
				
?>
				<form action="index.php?podstrona=usun" method="POST">
				    <nav class="navbar navbar-dark bg-dark nav-button">
				      <span class="navbar-brand mb-0 h1"><?php include "przyciski.php"; ?></span>
				    </nav>

					<table id="usrTable" class="tableT" cellpadding="2">
						<tr>
							<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
							<th id="defaultSort_th">Konto</th>
							<th>Nazwa wyświetlana</th>
							<th>UID</th>
							<th>Katalog WINDOWS</th>
							<th>Katalog UNIX</th>
							<th>Grupa</th>
							<th>Data wygaśnięcia</th>
							<th>Data utworzenia</th>
							<th>Ostatnie logowanie</th>
							<th>Status</th>
						</tr>
<?php
						for ($entry_num = 0; $entry_num < /*100*/ $result_entries['count']; $entry_num++) {
							// na strone
							flush();
							// parsowanie CN w poszukiwaniu grupy
							$grp = @$result_entries[$entry_num]['memberof'][0];
							$expl_grp = explode(',',$grp);
							$expl_grp_eq = explode("=",$expl_grp[0]);
							@$curr_grupa = $expl_grp_eq[1];
							
							// konwersja daty wygaśnięcia
							if ($result_entries[$entry_num]['accountexpires'][0] == "9223372036854775807" or $result_entries[$entry_num]['accountexpires'][0] == "0"){
								$exp_date = " ";
							} else{
								$exp_date = date("Y-m-d",ldapTimeToUnixTime($result_entries[$entry_num]['accountexpires'][0]));
							}
							
							$lastlogon = date("Y-m-d",ldapTimeToUnixTime($result_entries[$entry_num]['lastlogontimestamp'][0]));
							if ($lastlogon == "1601-01-01"){
								$lastlogon = "";
							}
?>						
							<tr>
								<td><input type="checkbox" name="delikwent[]" value="<?php echo $result_entries[$entry_num]['dn']; ?>"> </td>
								<td><?php echo @$result_entries[$entry_num]['uid'][0]; ?> </td>
								<td><?php echo @$result_entries[$entry_num]['displayname'][0]; ?> </td>
								<td align=center><?php echo @$result_entries[$entry_num]['uidnumber'][0]; ?> </td>
								<td><?php echo @$result_entries[$entry_num]['homedirectory'][0]; ?> </td>
								<td><?php echo @$result_entries[$entry_num]['unixhomedirectory'][0]; ?> </td>
								<td><?php echo $curr_grupa ?> </td>
								<td align=center><?php 
										if ($exp_date < date("Y-m-d")) {
											echo "<b><font color=red>";
											echo $exp_date;
											echo "</font></b>";
										} else {
											echo $exp_date;
										}
									?></td>
								<td align=center><?php echo @convertLdapTimeStamp(@$result_entries[$entry_num]['whencreated'][0],true); ?> </td>
								<td align=center><?php echo $lastlogon ?></td>
								<td align=right><?php echo getUserAccountControlAttributesPicture(@$result_entries[$entry_num]['useraccountcontrol'][0]); if ($_SESSION['enableZimbra']){echo getUserMailAccountPicture(@$result_entries[$entry_num]['uid'][0], $api);} ?></td>
							</tr>
<?php
				}
?>
					</table>
				</form>
<?php
			}
			else {
?>
				<p>Zwrócone przez serwer LDAP dane są nieprawidłowe.</p>
<?php
			}
		}
		else {
?>
			<p>Błąd podczas nawiązywania połączenia z serwerem LDAP.</p>
<?php
		}
	}
?>
<script>

$(document).ready(function() {
	//sortowanie po dacie
	$('#usrTable th').each(function(col) {
		$(this).click(function() {
			if ($(this).is('.asc')) {
				$(this).removeClass('asc');
				$(this).addClass('desc selected');
				sortOrder = -1;
			} else {
				$(this).addClass('asc selected');
				$(this).removeClass('desc');
				sortOrder = 1;
			}
			$(this).siblings().removeClass('asc selected');
			$(this).siblings().removeClass('desc selected');
			var tabData = $('#usrTable').find('tbody >tr:has(td)').get();

			tabData.sort(function(a, b) {
				var val1 = $(a).children('td').eq(col).text().toUpperCase();
				var val2 = $(b).children('td').eq(col).text().toUpperCase();
				if ($.isNumeric(val1) && $.isNumeric(val2))
					return sortOrder == 1 ? val1 - val2 : val2 - val1;
				else
					return (val1 < val2) ? -sortOrder : (val1 > val2) ? sortOrder : 0;
			});
      
			$.each(tabData, function(index, row) {
				$('tbody').append(row);
			});
		});
	});
	
	$('#defaultSort_th').click()
	$('#usrTable th').first().unbind("click");
});
</script>


