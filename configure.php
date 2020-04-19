<?php
require ("GoogleAPI/vendor/autoload.php");

$g_client = new Google_Client();


//passing the google credentials
$g_client->setClientId("311181393646-qolclkoo6u45cueb9fjoprec3rovhl1a.apps.googleusercontent.com");
$g_client->setClientSecret("r6ChxdScK3MwLJa94ABCPoeu");
$g_client->setRedirectUri("http://localhost/FinalProjectAPI-MelJasonChongWoYuen/index.php");
$g_client->setScopes("email");

//starting the session 
session_start();