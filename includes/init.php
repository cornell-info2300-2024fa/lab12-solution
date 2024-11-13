<?php

// Initialize all CSS class variables
$nav_home_class = "";
$nav_citations_class = "";
$nav_cooking_class = "";
$nav_flowershop_class = "";
$nav_transcript_class = "";
$nav_reviews_class = "";
$nav_plopbox_class = "";

// initialize and open database
require_once "includes/db.php";
$db = init_sqlite_db("db/site.sqlite", "db/init.sql");

// check login/logout params
require_once "includes/sessions.php";
$session_messages = array();
process_session_params($db, $session_messages);
