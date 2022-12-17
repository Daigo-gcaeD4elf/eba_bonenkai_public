<?php
session_start();

require_once('quiz_func.php');

$quizFunc = new QuizFunc();

// quiz_id取得
$quizId = '';
if (!empty($_GET['quiz_id'])) {
    $quizId = $_GET['quiz_id'];
}

// confから戻ってきた場合は、入力したデータで配列作る(最優先！！)
// 新規作成の場合は、デフォルトで4項目の配列作成
// 編集の場合、データベースからクイズデータ取ってくる
$quiz = [];
$quizOptionTag = [];

// confから戻ってきた場合
if (!empty($_POST['create_quiz_conf'])) {
    $quiz = [
        'quiz_question_sentence' => $_POST['quiz_question_sentence'],
        'quiz_answer' => $_POST['quiz_answer'],
        'time_limit' => $_POST['time_limit'],
        'allocation' => $_POST['allocation'],
        'fifty_fifty_chooseable' => $_POST['fifty_fifty_chooseable'],
    ];

    $i = 0;
    $quizOptionTag = [];
    while (!empty($_POST['quiz_option_id_'. $i])) {
        $optionArray = [
            'quiz_option_id' => $_POST['quiz_option_id_'. $i],
            'quiz_option_text' => $_POST['quiz_option_text_'. $i],
            'fifty_fifty_display' => $_POST['fifty_fifty_display_'. $i],
        ];
        array_push($quizOptionTag, $optionArray);
        $i = $i + 1;
    }
}

// 編集の場合
if (!empty($_GET['quiz_id']) && empty($quiz)) {
    try {
        $quiz = $quizFunc->fetchQuiz($_GET['quiz_id']);
        $quizOptionTag = $quizFunc->fetchQuizOption($_GET['quiz_id']);
    } catch(Exception $e) {
        writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
    }
}

if (empty($quiz)) {
    $quiz = [];
    $quizOptionTag = [
        [
            'quiz_option_id' => 1,
            'quiz_option_text' => '',
            'fifty_fifty_display' => '1',
        ],
        [
            'quiz_option_id' => 2,
            'quiz_option_text' => '',
            'fifty_fifty_display' => '1',
        ],
        [
            'quiz_option_id' => 3,
            'quiz_option_text' => '',
            'fifty_fifty_display' => '1',
        ],
        [
            'quiz_option_id' => 4,
            'quiz_option_text' => '',
            'fifty_fifty_display' => '1',
        ],
    ];
}

$quizOptionTagJson = json_encode($quizOptionTag);

require_once('../html/create_quiz_edit.html');
