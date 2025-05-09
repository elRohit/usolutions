<?php
require_once '../includes/functions.php';

// Log the user out
logout();

// Redirect to home page
header("Location: ../pages/home.php");
exit;

