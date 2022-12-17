<?php
// 結果取得 ※獲得ポイントもここから！！
try {
    // 累計獲得ポイント表示
    $yourTotalScore = $quizFunc->getMemberTotalScoreFromResultTable($nowQuizAdminStatus['now_game_id'], $_SESSION['member_id']);

    //
    $yourResult = $quizFunc->getMemberResult($nowQuizAdminStatus['now_game_id'], $_SESSION['member_id']);

    // 現在のランキング取得
    $totalScore = $quizFunc->getTotalScoreFromResultTable($nowQuizAdminStatus['now_game_id'], MAX_FETCH_RANKING);
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_finish.html');