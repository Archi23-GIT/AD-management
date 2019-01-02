<?php
	unset($_SESSION['valid']);
	unset($_SESSION['timeout']);
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	header('Location: index.php');
?>
