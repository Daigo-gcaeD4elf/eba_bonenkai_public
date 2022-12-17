<?php
session_start();
require_once('quiz_func.php');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_decide_answer');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_game_ask_question') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

try {
    // quiz_admin_statusのstatusを「答え合わせ待機中」に更新
    $quizFunc->updateQuizStatus(WAITING_ANSWER_STATUS);

    // 現在のクイズを取得
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();
    // クイズIDからクイズと選択肢を取得
    $nowQuiz = $quizFunc->fetchQuiz($nowQuizAdminStatus['now_quiz_id']);
    $nowQuizOption = $quizFunc->fetchQuizOption($nowQuizAdminStatus['now_quiz_id']);

    // 現在のランキング取得
    $totalScore = $quizFunc->getTotalScore($nowQuizAdminStatus['now_game_id'], MAX_FETCH_RANKING);

    // メンバーの回答を取得
    $memberAnswers = $quizFunc->fetchMemberAnswers($nowQuizAdminStatus);
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_admin_game_decide_answer.html');

