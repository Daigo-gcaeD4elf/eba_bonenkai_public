<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_finish');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_game_result') === false && strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_game_finish_detail') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

try {
    // ステータスを更新してユーザー画面を結果発表に遷移させる
    $quizFunc->updateQuizStatus(FINISH_GAME_STATUS);

    // 現在の状況を取得
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();

    // 現在のランキング取得
    $totalScore = $quizFunc->getTotalScoreFromResultTable($nowQuizAdminStatus['now_game_id']);
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_admin_game_finish.html');
