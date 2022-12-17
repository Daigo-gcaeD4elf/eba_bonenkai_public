<?php

// クイズIDからクイズと選択肢を取得
$nowQuiz = $quizFunc->fetchQuiz($nowQuizAdminStatus['now_quiz_id']);
$nowQuizOption = $quizFunc->fetchQuizOption($nowQuizAdminStatus['now_quiz_id']);

// メンバーの回答を取得
$memberAnswers = $quizFunc->fetchMemberAnswers($nowQuizAdminStatus);

$yourResult = $quizFunc->fetchQuizYourResult($nowQuizAdminStatus, $_SESSION['member_id']);

// 自身の回答取得 (スーパーさとしくんもここで出す)
// $yourAnswer = $quizFunc->fetchYourAnswer();

require_once('../html/quiz_waiting_answer.html');