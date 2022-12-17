<?php
// 結果取得 ※獲得ポイントもここから！！
try {
    $correctAnswer = $quizFunc->fetchCorrectAnswer($nowQuizAdminStatus['now_game_id'], $nowQuizAdminStatus['now_game_order']);
    $yourResult = $quizFunc->fetchQuizYourResult($nowQuizAdminStatus, $_SESSION['member_id']);

    $nowQuiz = $quizFunc->fetchQuiz($nowQuizAdminStatus['now_quiz_id']);

    // 累計獲得ポイント表示
    $totalPoint = $quizFunc->getTotalPoint($nowQuizAdminStatus['now_game_id'], $_SESSION['member_id']);

    // 正解者を取得
    $correctAnswerMembers = $quizFunc->fetchCorrectAnswerMembers($nowQuizAdminStatus['now_game_id'], $nowQuizAdminStatus['now_game_order']);
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_result.html');
