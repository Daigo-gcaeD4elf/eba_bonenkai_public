<?php
session_start();
require_once('quiz_func.php');

writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'quiz_admin_game_start');
writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '前のページは、'. $_SERVER['HTTP_REFERER']);

if (empty($_POST['quiz_start'])) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}

if (strpos($_SERVER['HTTP_REFERER'], 'quiz_admin_choose_playlist') === false) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, '誤作動しました。');
    exit();
}


$quizFunc = new QuizFunc();

// ゲームスタート

$quizGameTitle = '';
if (!empty($_POST['quiz_game_title'])) {
    $quizGameTitle = $_POST['quiz_game_title'];
}

// ゲームを登録、ゲームID設定
try {
    $gameId = $quizFunc->registerQuizGameId($quizGameTitle);

    // quiz_admin_statusテーブルにゲームIDとプレイリストIDを登録する
    $quizFunc->resetAdminStatusTable($gameId, $_POST['selected_quiz_playlist_id'], STAND_BY_STATUS);

    // 現段階でログインしているメンバーを参加者とする
    $quizFunc->resisterGameMember($gameId);

    // メンバー回答テーブルをリセット(DELETE)
    $quizFunc->resetMemberAnswerTable();

} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}
// 出題画面へジャンプ
header('Location: ./quiz_admin_game_ask_question.php');
exit();
