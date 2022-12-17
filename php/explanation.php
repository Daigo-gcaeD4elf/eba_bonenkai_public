<?php
session_start();

require_once('Common.php');
require_once('Db.php');
require_once('DbCommon.php');

if (empty($_SESSION['member_id'])) {

    if (empty($_POST['member_id'])) {
        session_destroy();
        header('Location: ../html/login.html');
        exit;
    }

    $_SESSION['member_id'] = $_POST['member_id'];
}

class Explanation extends DbCommon
{

}

$exp = new Explanation();
$userData = $exp->getUserData($_SESSION['member_id']);

require_once('../html/explanation.html');
