<?php
session_start();

require_once('quiz_func.php');

// playlist_id取得
$playlistId = '';
if (!empty($_GET['playlist_id'])) {
    $playlistId = $_GET['playlist_id'];
}

$quizFunc = new QuizFunc();
// クイズIDから問題文取得
$i = 0;
while (!empty($_POST['playlist_order_'. $i])) {
    $_POST['quiz_question_sentence_'. $i] = $quizFunc->fetchQuizSentence($_POST['quiz_id_'. $i]);
    $i = $i + 1;
}

require_once('../html/create_quiz_playlist_conf.html');
