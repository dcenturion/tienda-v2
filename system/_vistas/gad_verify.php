<?php
session_start();
error_reporting( E_ERROR );

$room_access = $_SESSION["room_access"];
$administrator_access = $_SESSION["administrator_access"];
$master_access = $_SESSION["master_access"];

$success = $room_access || $administrator_access || $master_access;

$success || exit;