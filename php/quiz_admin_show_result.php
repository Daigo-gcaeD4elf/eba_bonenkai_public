<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_result');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'admin') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

try {
    $quizGameHistory = $quizFunc->fetchQuizGameHistory();
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_admin_show_result.html');
