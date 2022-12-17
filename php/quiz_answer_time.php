<?php
try {
    // クイズを取得
    $nowQuiz = $quizFunc->fetchQuiz($nowQuizAdminStatus['now_quiz_id']);

    // クイズ選択肢を取得
    $nowQuizOption = $quizFunc->fetchQuizOption($nowQuizAdminStatus['now_quiz_id']);

    // ライフラインボタン押下時 quiz_member_answerテーブルを更新
    if (!empty($_POST['use_fifty_fifty'])) {
        $quizFunc->useFiftyFifty($nowQuizAdminStatus, $_SESSION['member_id']);
    }
    if (!empty($_POST['use_audience'])) {
        $quizFunc->useAudience($nowQuizAdminStatus, $_SESSION['member_id']);
    }

    // 回答状況を取得
    $defaultMemberStatus = $quizFunc->fetchUserAnswer($nowQuizAdminStatus, $_SESSION['member_id']);
    $defaultAnswer          = $defaultMemberStatus['member_answer'];
    $isUsingSuperSatoshikun = $defaultMemberStatus['is_using_super_satoshikun'];
    $isUsingFiftyFifty      = $defaultMemberStatus['is_using_fifty_fifty'];
    $isUsingAudience        = $defaultMemberStatus['is_using_audience'];

    // スーパーさとしくんを使える回数
    $superSatoshikunPowerPoint = $nowQuizAdminStatus['super_satoshikun_power_point'];

    // スーパーさとしくんを使った回数
    $superSatoshikunStock = $quizFunc->fetchSuperSatoshikunStock($nowQuizAdminStatus, $_SESSION['member_id']);

    // ライフラインの残機
    $lifeLineStockLists = $quizFunc->fetchLifeLineStock($nowQuizAdminStatus, $_SESSION['member_id']);
    $fiftyFiftyStock = $lifeLineStockLists['fifty_fifty_stock'];
    $audienceStock = $lifeLineStockLists['audience_stock'];

    // 50-50選択可否
    $fiftyFiftyChooseAble = ($fiftyFiftyStock > 0) ? '1' : '0';
    // 残機が残っていても50-50を選ぶことができない問題が存在
    if ($nowQuiz['fifty_fifty_chooseable'] === '0') {
        $fiftyFiftyChooseAble = '0';
    }

    // オーディエンス選択可否
    $audienceChooseAble = ($audienceStock > 0) ? '1' : '0';
    // 残機が残っていても50-50を選ぶことができない問題が存在
    if ($nowQuiz['audience_chooseable'] === '0') {
        $audienceChooseAble = '0';
    }

    // オーディエンス選択時、参加者の回答を取得
    $memberAnswersJson = '';
    if ($isUsingAudience === '1') {
        $memberAnswersJson = json_encode($quizFunc->fetchNumberOfMemberAnswers($nowQuizAdminStatus));
    }

    // 制限時間計算 (ブラウザ更新対策)
    $timeLimit = $quizFunc->calcTimeLimit();

    if (empty($timeLimit) || $timeLimit < 0) {
        $timeLimit = 0;
    }

    // 何かしらの原因で、stateがASK_QUESTION_STATUSでないのにこのPHPが呼ばれた場合
    if ($nowQuizAdminStatus['now_state'] != ASK_QUESTION_STATUS) {
        $timeLimit = 0;
    }

} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

// これまでの戦績を取得 獲得したポイント、その経緯(これはeba_quizで取ってくるか…？？)

require_once('../html/quiz_answer_time.html');
