<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_ask_question');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_choose_playlist') === false && strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_game_result') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

$quizFunc = new QuizFunc();

// クイズを出題
try {
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();
    $bakStatus = $nowQuizAdminStatus['now_state'];

    // プレイリストに沿って出題されたかどうか
    $onPlayList = true;

    // admin_statusテーブルの出題番号を+1, プレイリスト順にクイズを出していればプレイリストオーダーも+1
    if ($bakStatus != ASK_QUESTION_STATUS) {
        $quizFunc->goNextOrder($onPlayList);
    }

    // 現在の状況を取得
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();

    // プレイリストテーブルからクイズID取得
    $nowQuizId = $quizFunc->fetchQuizIdFromPlayList($nowQuizAdminStatus['now_playlist_id'], $nowQuizAdminStatus['now_game_order']);

    // クイズIDからクイズと選択肢を取得
    $nowQuiz = $quizFunc->fetchQuiz($nowQuizId);
    $nowQuizOption = $quizFunc->fetchQuizOption($nowQuizId);

    if ($bakStatus != ASK_QUESTION_STATUS) {
        // 出題履歴テーブルにINSERT
        $quizFunc->resisterQuizAskedHistory($nowQuizAdminStatus, $nowQuiz);

        // admin_statusテーブルのクイズID更新
        $quizFunc->updateAdminQuizId($nowQuizId);

        // member_answerテーブルに回答用レコードを準備
        $quizFunc->setMemberAnswerTable($nowQuizAdminStatus['now_game_id'], $nowQuizAdminStatus['now_game_order']);

        // admin_statusを更新して、ユーザー画面を遷移させる
        $quizFunc->updateQuizStatus(ASK_QUESTION_STATUS);
    }

    // 制限時間取得(ブラウザ更新対策)
    $timeLimit = $quizFunc->calcTimeLimit();

    // 現在のランキング取得
    $totalScore = $quizFunc->getTotalScore($nowQuizAdminStatus['now_game_id'], MAX_FETCH_RANKING);
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}
require_once('../html/quiz_admin_game_ask_question.html');
