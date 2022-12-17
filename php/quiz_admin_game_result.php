<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_result');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_game_decide_answer') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

try {
    // ステータスを更新してユーザー画面を結果発表に遷移させる
    $quizFunc->updateQuizStatus(RESULT_STATUS);

    // 現在の状況を取得
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();

    // クイズIDからクイズと選択肢を取得
    $nowQuiz = $quizFunc->fetchQuiz($nowQuizAdminStatus['now_quiz_id']);
    $nowQuizOption = $quizFunc->fetchQuizOption($nowQuizAdminStatus['now_quiz_id']);

    // 正解を取得
    $correctAnswer = $quizFunc->fetchCorrectAnswer($nowQuizAdminStatus['now_game_id'], $nowQuizAdminStatus['now_game_order']);

    // 正解者を取得
    $correctAnswerMembers = $quizFunc->fetchCorrectAnswerMembers($nowQuizAdminStatus['now_game_id'], $nowQuizAdminStatus['now_game_order']);

    // 現在のランキング取得
    $totalScore = $quizFunc->getTotalScore($nowQuizAdminStatus['now_game_id'], MAX_FETCH_RANKING);

    // 次の問題を取得(無い場合も有り)
    $nextQuizId = $quizFunc->fetchQuizIdFromPlayList($nowQuizAdminStatus['now_playlist_id'], $nowQuizAdminStatus['now_game_order'] + 1);
    $nextQuiz = [];
    if (!empty($nextQuizId)) {
        $nextQuiz = $quizFunc->fetchQuiz($nextQuizId);
    }

} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_admin_game_result.html');
