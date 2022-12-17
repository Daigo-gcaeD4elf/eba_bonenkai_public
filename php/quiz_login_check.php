<?php
if (empty($_SESSION['member_id'])) {

    if (empty($_POST['member_id'])) {
        session_destroy();
        header('Location: ../html/quiz_login.html');
        exit;
    }

    $_SESSION['member_id'] = $_POST['member_id'];
}
