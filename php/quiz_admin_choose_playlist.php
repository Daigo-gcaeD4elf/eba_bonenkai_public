<?php
session_start();
require_once('quiz_func.php');

$quizFunc = new QuizFunc();

// プレイリスト一覧取得
$quizPlaylists = $quizFunc->getCreatedQuizPlaylists();

require_once('../html/quiz_admin_choose_playlist.html');
