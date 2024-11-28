<?php
require_once 'config/config.php';

// Distruge sesiunea
session_destroy();

// Redirecționează către pagina de login
redirect('login.php');
?> 