<?php
session_start();

require_once('quiz_func.php');

$quizId = '';
if (!empty($_GET['quiz_id'])) {
    $quizId = $_GET['quiz_id'];
}

require_once('../html/create_quiz_conf.html');
