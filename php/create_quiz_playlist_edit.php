<?php
session_start();

require_once('quiz_func.php');

// playlist_id取得
$playlistId = '';
if (!empty($_GET['playlist_id'])) {
    $playlistId = $_GET['playlist_id'];
}

$quizFunc = new QuizFunc();

// クイズを取得しておく
$createdQuizzes = $quizFunc->getCreatedQuizzes();

// confから戻ってきた場合
if (!empty($_POST['create_quiz_playlist_conf'])) {
    $playlist = [
        'playlist_title' => $_POST['playlist_title'],
        'super_satoshikun_power_point' => $_POST['super_satoshikun_power_point'],
        'fifty_fifty_power_point' => $_POST['fifty_fifty_power_point'],
        'audience_power_point' => $_POST['audience_power_point'],
    ];

    $i = 0;
    $playlistOrder = [];
    while (!empty($_POST['playlist_order_'. $i])) {
        $orderArray = [
            'quiz_id' => $_POST['quiz_id_'. $i],
        ];
        array_push($playlistOrder, $orderArray);
        $i = $i + 1;
    }
}

// 編集の場合
if (!empty($_GET['playlist_id']) && empty($playlist)) {
    $playlist = $quizFunc->fetchQuizPlaylist($_GET['playlist_id']);
    $playlistOrder = $quizFunc->fetchQuizPlaylistOrder($_GET['playlist_id']);
}

if (empty($playlist)) {
    $playlist = [];
    $playlistOrder = [
        [
            'quiz_id' => '1',
        ],
    ];
}

$playlistOrderJson = json_encode($playlistOrder);
$createdQuizzesJson = json_encode($createdQuizzes);

// プルダウンに表示するため、改行を削除
$createdQuizzesJson = str_replace(['\r\n', '\r', '\n'], '', $createdQuizzesJson);

require_once('../html/create_quiz_playlist_edit.html');
