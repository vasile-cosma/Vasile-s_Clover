<?php
session_start();
session_regenerate_id();
require_once __DIR__ . "/../data-access/BlackjackDataAccess.php";
require_once __DIR__ . "/../utils/SecUtils.php";

$dbFile = __DIR__ . "/../database/blackjack.db";

$blackjackDataAccess = new BlackjackDataAccess($dbFile);