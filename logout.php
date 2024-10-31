<?php
session_start();
session_destroy(); // Uništava sve sesije
header("Location: login.html"); // Preusmeri na stranicu za prijavu
exit();
?>
