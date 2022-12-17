<?php
session_start();

require_once('quiz_func.php');

$showCreatedQuizzes = new QuizFunc();

$msg = '';
if (!empty($_POST['quiz_delete'])) {
    // var_dump($_POST);
    try {
        $showCreatedQuizzes->deleteQuiz($_POST['quiz_id']);
    } catch (Exception $e) {
        writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
    }

}

$quizzes = $showCreatedQuizzes->getCreatedQuizzes();
require_once('../html/show_created_quizzes.html');
