<?php
include('configure.php');

$g_client->revokeToken();
session_destroy();

header('Location: index.php');


?>