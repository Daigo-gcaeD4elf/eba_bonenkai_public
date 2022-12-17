<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_check_members_answer');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_game_decide_answer') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

try {
    // 現在の状況を取得
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();
    $nowQuiz = $quizFunc->fetchQuiz($nowQuizAdminStatus['now_quiz_id']);

    // クイズテーブル(quiz)に正解を登録
    $quizFunc->updateCorrectAnswerToQuiz($nowQuizAdminStatus['now_quiz_id'], $_POST['correct_answer']);

    // クイズ履歴テーブル(quiz_asked_history)に正解を登録
    $quizFunc->updateCorrectAnswerToQuizAskedHistory($nowQuizAdminStatus['now_game_id'], $nowQuizAdminStatus['now_game_order'], $_POST['correct_answer']);

    // 正解者の正解不正解フラグを「正解」に更新
    $quizFunc->checkMemberAnswer($nowQuizAdminStatus, $_POST['correct_answer']);

    // quiz_member_answerのレコードを元に、quiz_resultテーブルへINSERT
    // 正解の場合は、quizテーブルの配点をquiz_resultテーブルのacquired_pointとする
    // スーパーさとしくん使用の場合は得点を2倍にして登録
    $quizFunc->resisterResult($nowQuizAdminStatus, $nowQuiz);
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

// 結果発表ページへジャンプ
header('Location: ./quiz_admin_game_result.php');
exit();
