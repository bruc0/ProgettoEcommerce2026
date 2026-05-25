<?php
session_start();
unset($_SESSION['utente_id'], $_SESSION['utente_nome']);
header('Location: index.php');
exit;
