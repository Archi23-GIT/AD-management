<?php
	session_start();
	
	include "config.php";
	include 'funkcje.php';
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_STRICT);
	ini_set('display_errors', 1);
	ini_set('html_errors', 1);

	if (empty($_GET['podstrona'])) {
		header('Location: index.php?podstrona=zaloguj');
	}
?>
	<html lang="pl">
		<head>
			<meta charset="UTF-8">
			<link rel="stylesheet" href="style.css">
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
			<title>AD-Manager</title>
		</head>
		<body>
		<?php
				if (!isset($_SESSION['valid'])) {
?>
		<nav class="navbar navbar-dark bg-dark navbar-fixedtop">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php?podstrona=glowna">
						<img src="images/adm.png" height="40" class="d-inline-block align-top" alt="">
						AD-Manager
					</a>
				</div>
				<div class="pull-right">
					<ul class="nav navbar-nav">
						<li><button class="btn navbar-btn btn-success" name="login" id="login"  value="Log In" onclick="location.href='index.php?podstrona=zaloguj';"><?php echo lang_msg("Login") ?></button></li>
					</ul>     
				</div>
		</nav>
<?php
				}
				else {
?>
		<nav class="navbar navbar-dark bg-dark navbar-fixedtop">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php?podstrona=glowna">
						<img src="images/adm.png" height="40" class="d-inline-block align-top" alt="">
						<?php echo $_SESSION['domain'] ?>
					</a>
				</div>
				<ul class="navbar-nav bd-navbar-nav flex-row mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="index.php?podstrona=dodaj_jeden"><?php echo lang_msg("Single ADD") ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php?podstrona=dodaj_plik"><?php echo lang_msg("Multi ADD") ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php?podstrona=lista"><?php echo lang_msg("Active accounts") ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php?podstrona=lista-usuniete"><?php echo lang_msg("Expired accounts") ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php?podstrona=lista-zablokowane"><?php echo lang_msg("Locked accounts") ?></a>
					</li>
				</ul>
				<div class="pull-right">
					<ul class="nav navbar-nav flex-row mr-auto">
						<li  class="nav-item">
						
						<form method="post" action="index.php?podstrona=find"><input type="text" name="Find" id="Find" title="Search in (cn,givenname,surname,uid)" placeholder="Szukaj"></input><button type="submit" name="find" id="findbt" value="Find" hidden="hidden"></button></form>
						</li>
						<li  class="nav-item">
						<button class="btn navbar-btn btn-danger" name="logout" id="logout"  value="Log Out" onclick="location.href='index.php?podstrona=wyloguj';"><?php echo lang_msg("Logout") ?></button>
						
						</li>
					</ul>     
				</div>
		</nav>
<?php
				}
?>
		
		<div  style="background-color: #fff;border: 1px solid #212529;border-radius: 10px;margin-left: 160px;padding-left: 12px;margin-right: calc(0% + 10px);">

<?php
	
	if ($_GET['podstrona'] == 'zaloguj') {
		if (isset($_SESSION['valid'])) {
			header('Location: index.php?podstrona=glowna');
		}
		else {
			include "zaloguj.php";
		}
	}
	else if ($_GET['podstrona'] == 'glowna') {
		if (isset($_SESSION['valid'])) {
			include "glowna.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'lista') {
		if (isset($_SESSION['valid'])) {
			include "lista.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'find') {
		if (isset($_SESSION['valid'])) {
			include "lista-find.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'lista-usuniete') {
		if (isset($_SESSION['valid'])) {
			include "lista-expire.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'lista-zablokowane') {
		if (isset($_SESSION['valid'])) {
			include "lista-zablokowane.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'dodaj_jeden') {
		if (isset($_SESSION['valid'])) {
			include "dodaj_jeden.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'dodaj_plik') {
		if (isset($_SESSION['valid'])) {
			include "dodaj_plik.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'obiegowka') {
		if (isset($_SESSION['valid'])) {
			include "obiegowka.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'blokuj') {
		if (isset($_SESSION['valid'])) {
			include "blokuj.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'odblokuj') {
		if (isset($_SESSION['valid'])) {
			include "odblokuj.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'usun') {
		if (isset($_SESSION['valid'])) {
			include "usun.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
	else if ($_GET['podstrona'] == 'wyloguj') {
		if (isset($_SESSION['valid'])) {
			include "wyloguj.php";
		}
		else {
			header('Location: index.php?podstrona=zaloguj');
		}
	}
?>
		</div>
		<footer class="footer-dark bg-dark footer-positioning fixed-bottom">
			<span class="text" style="padding-left: 1em">&copy; 2019 <A HREF="http://gsliwinski.strony.wi.ps.pl/">Grzegorz Śliwiński</A> - <A HREF="https://www.wi.zut.edu.pl/">WIZUT</A> - version 0.7 ( <i><?php echo $_SESSION['lang']?></i> )</span>
		</footer>
		
		<script>
		var input = document.getElementById("Find");
		input.addEventListener("keyup", function(event) {
  			if (event.keyCode === 13) {
   				event.preventDefault();
   				document.getElementById("findbt").submit();
  			}
		});
</script>
		
		</body>
	</html>
