<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_finish');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_game_finish') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

try {
    // 現在の状況を取得
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();

    // 累計獲得ポイント表示
    $memberTotalScore = $quizFunc->getMemberTotalScoreFromResultTable($_POST['game_id'], $_POST['member_id']);

    //
    $memberResult = $quizFunc->getMemberResult($_POST['game_id'], $_POST['member_id']);

} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_admin_game_finish_detail.html');
