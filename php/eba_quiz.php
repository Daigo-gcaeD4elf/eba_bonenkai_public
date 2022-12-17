<?php
session_start();

require_once('quiz_login_check.php');
require_once('quiz_func.php');

$quizFunc = new QuizFunc();

class Routing extends Db
{
    private $pages = [
        STAND_BY_STATUS => 'quiz_stand_by',
        ASK_QUESTION_STATUS => 'quiz_answer_time',
        WAITING_ANSWER_STATUS => 'quiz_waiting_answer',
        RESULT_STATUS => 'quiz_result',
        FINISH_GAME_STATUS => 'quiz_finish',
    ];

    /**
     * 表示するページを取得
     * @return string
     */
    public function judgeRoutingPage()
    {
        $sql = 'SELECT now_state FROM quiz_admin_status';
        $stmt = $this->dbh->query($sql);
        $quizGameState = $stmt->fetch(PDO::FETCH_COLUMN);

        if (empty($quizGameState)) {
            $quizGameState = STAND_BY_STATUS;
        }
        return $this->pages[$quizGameState];
    }

    // デバッグ用
    public function debugLog($pageName)
    {
        $sql = <<< EOF
            INSERT INTO login_error (
                entry_time
                ,inputted_id
                ,inputted_password
            ) VALUES (
                NOW()
                ,'routing'
                ,'{$pageName}'
            );
EOF;
        $this->dbh->query($sql);
    }
}

try {
    // 現在の状況を取得
    $nowQuizAdminStatus = $quizFunc->fetchQuizAdminStatus();

    // ユーザー情報取得
    $userData = $quizFunc->fetchMemberData($_SESSION['member_id']);

    // 獲得したポイントを取得
    $totalScore = [];
    $totalScore['total_point'] = 0;
    $totalScore['ranking']     = 0;
    if (!empty($nowQuizAdminStatus)) {
        $yourTotalScore = $quizFunc->getMemberTotalScore($nowQuizAdminStatus['now_game_id'], $_SESSION['member_id']);
        $totalScore = $quizFunc->getTotalScore($nowQuizAdminStatus['now_game_id'], MAX_FETCH_RANKING);
    }

    $routing = new Routing();

    $pageName = $routing->judgeRoutingPage();
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, 'メンバーID:'. $_SESSION['member_id']. '、'.  $e->getMessage());
}

require_once($pageName.'.php');
