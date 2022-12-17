<?php
session_start();

require_once('quiz_func.php');

$quizFunc = new QuizFunc();

$confMsg = '';
if (!empty($_GET['playlist_id'])) {
    $quizFunc->updateQuizPlaylist($_POST, $_GET['playlist_id']);
    $confMsg = 'プレイリストを修正しました';
} else {
    $quizFunc->createNewQuizPlaylist($_POST);
    $confMsg = 'プレイリストを登録しました';
}

// クイズIDから問題文取得
$i = 0;
while (!empty($_POST['playlist_order_'. $i])) {
    $_POST['quiz_question_sentence_'. $i] = $quizFunc->fetchQuizSentence($_POST['quiz_id_'. $i]);
    $i = $i + 1;
}

require_once('../html/create_quiz_playlist_done.html');
