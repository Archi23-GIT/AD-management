<?php

if (isset($_POST['usun'])) {
	$ldapuser = $_SESSION['username'];
	$ldappass = $_SESSION['password'];
	$ldap_con = ldap_connect($_SESSION['ldaphost']) or die("<br>".lang_msg("Connection problem...")."<br>");
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	
	$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die(lang_msg("Failed as admin ..."));
	
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
	$ldap_con = ldap_connect($_SESSION['ldaphost']) or die("<br>".lang_msg("Connection problem...")."<br>");
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);
	
	$conn = ldap_bind($ldap_con, $ldapuser, $ldappass) or die(lang_msg("Failed as admin ..."));
	?>

		<h3><b><?php echo lang_msg("Search result") ?></b>

<?php
		if ($conn) {
			
			$coszukasz = $_POST['Find'];
			
			echo "[<i>".$coszukasz;
			
			//echo "Filter:".$_SESSION['filter'];
			$search_result = ldap_search($ldap_con, $_SESSION['basedn'], "(|(uid=*".$coszukasz."*)(cn=*".$coszukasz."*)(sn=".$coszukasz."*)(givenname=".$coszukasz."*))",$_SESSION['attr']);
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
				
				// Wyswietlanie liczny znalezionych po tytule
				echo "</i>] - ".lang_msg("found")." ".$result_entries['count']." ".lang_msg("perons")."</h3>";
?>
				<form action="index.php?podstrona=usun" method="POST">
				    <nav class="navbar navbar-dark bg-dark nav-button">
				      <span class="navbar-brand mb-0 h1"><?php include "przyciski.php"; ?></span>
				    </nav>

					<table id="usrTable" class="tableT" cellpadding="2">
						<tr>
							<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
							<th id="defaultSort_th"><?php echo lang_msg("Account") ?></th>
							<th><?php echo lang_msg("Display name") ?></th>
							<th><?php echo lang_msg("Unix uid") ?></th>
							<th><?php echo lang_msg("WINDOWS directory") ?></th>
							<th><?php echo lang_msg("UNIX directory") ?></th>
							<th><?php echo lang_msg("Groups") ?></th>
							<th><?php echo lang_msg("Expiration date") ?></th>
							<th><?php echo lang_msg("Creation date") ?></th>
							<th><?php echo lang_msg("Last logon date") ?></th>
							<th><?php echo lang_msg("Status") ?></th>
						</tr>
<?php
						for ($entry_num = 0; $entry_num < /*100*/ $result_entries['count']; $entry_num++) {
							// na strone
							flush();
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
								<td><?php echo @$result_entries[$entry_num]['cn'][0]; ?> </td>
								<td><?php echo substr(@$result_entries[$entry_num]['displayname'][0],0,30); ?> </td>
								<td align=center><?php echo @$result_entries[$entry_num]['uidnumber'][0]; ?> </td>
								<td><?php echo @$result_entries[$entry_num]['homedirectory'][0]; ?> </td>
								<td><?php echo @$result_entries[$entry_num]['unixhomedirectory'][0]; ?> </td>
								<td><?php 
								//echo $curr_grupa 
								echo "<select style=\"width: 120;\">";
								for ($gp = 0; $gp < $result_entries[$entry_num]['memberof']['count']; $gp++) {
								$grp = @$result_entries[$entry_num]['memberof'][$gp];
								$expl_grp = explode(',',$grp);
								$expl_grp_eq = explode("=",$expl_grp[0]);
								echo "<option>".$expl_grp_eq[1]."</option>";
								}
								echo "</select>";
								?> </td>
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
				<p><?php echo lang_msg("The data returned by the LDAP server is incorrect.")?></p>
<?php
			}
		}
		else {
?>
			<p><?php echo lang_msg("Error while connecting to the LDAP server.")?></p>
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


