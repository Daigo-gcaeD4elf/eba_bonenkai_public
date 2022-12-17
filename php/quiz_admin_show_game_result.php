<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_finish');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_show_result') === false && strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_show_game_member_result') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

try {
    // 現在のランキング取得
    $totalScore = $quizFunc->getTotalScoreFromResultTable($_POST['game_id']);
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_admin_show_game_result.html');
