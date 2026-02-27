<?php
session_start();
require 'includes/db.php';
unset($_SESSION['user_id']);
header('Location: index.php');
exit;