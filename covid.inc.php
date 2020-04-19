<?php

if (isset($_POST['check'])) {
    $country = $_POST['country'];

    header('Location: ../FinalProjectAPI-MelJasonChongWoYuen/?country=' . $country);
    exit();
}