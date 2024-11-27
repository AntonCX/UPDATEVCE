<?php
require_once 'controller/FormController.php';
$formController = new FormController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formController->submit();
} else {
    $formController->index();
}
?>