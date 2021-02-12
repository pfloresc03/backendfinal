<?php
mb_internal_encoding("UTF-8");
date_default_timezone_set('Europe/Madrid');
header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('ROOT', dirname("../".__FILE__) . DIRECTORY_SEPARATOR);