<?php
session_start();

require_once('quiz_func.php');

$quizFunc = new QuizFunc();

// 削除
if (!empty($_POST['quiz_playlist_delete'])) {
    try {
        $quizFunc->deletePlayList($_POST['quiz_playlist_id']);
    } catch (Exception $e) {
        writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
    }
}

$quizPlaylists = $quizFunc->getCreatedQuizPlaylists();
require_once('../html/show_created_quiz_playlists.html');
