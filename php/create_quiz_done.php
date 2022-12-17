<?php
session_start();

require_once('quiz_func.php');

$quizFunc = new QuizFunc();

$confMsg = '';
if (!empty($_GET['quiz_id'])) {
    $quizFunc->updateQuiz($_POST, $_GET['quiz_id']);
    $confMsg = 'クイズを修正しました';
} else {
    $quizFunc->createNewQuiz($_POST);
    $confMsg = 'クイズを登録しました';
}

require_once('../html/create_quiz_done.html');
