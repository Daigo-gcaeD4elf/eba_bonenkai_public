<?php
session_start();

require_once('Common.php');
require_once('Db.php');
require_once('DbCommon.php');

class AdminPreResult extends DbCommon
{
    /**
     * ゲーム状態更新 (結果集計中)
     * @return void
    */
    public function changeGameState()
    {
        $sql = <<< EOF
            UPDATE
                admin_rock_paper_scissors
            SET
                state = 2
            WHERE
                admin_member_id = 1
            ;
EOF;
        $stmt = $this->dbh->query($sql);
    }

    /**
     * 勝敗判定およびDB更新
     * @return void
    */
    public function judgeWinOrLose()
    {
        $adminerRps = $this->getAdminerChoise();
        if ($adminerRps === '1') {
            $winnerRps = '3';
            $loserRps  = '2';
        } elseif ($adminerRps === '2') {
            $winnerRps = '1';
            $loserRps  = '3';
        } elseif ($adminerRps === '3') {
            $winnerRps = '2';
            $loserRps  = '1';
        }

        $mode = $this->getMode();
        if ($mode === '0') {
            $winnerRps .= ', '. $adminerRps;
        } else {
            $loserRps .= ', '. $adminerRps;
        }

        // 勝者の処理 勝利数を+1
        $sql = <<< EOF
            UPDATE
                rock_paper_scissors
            SET
                number_of_wins = number_of_wins + 1
            WHERE
                lose_flg = 0
                AND rps IN ({$winnerRps})
            ;
EOF;
        $stmt = $this->dbh->query($sql);

        // 敗者の処理 loseフラグを立てる
        $sql = <<< EOF
            UPDATE
                rock_paper_scissors
            SET
                lose_flg = 1
            WHERE
                lose_flg = 0
                AND rps IN ({$loserRps})
            ;
EOF;
        $stmt = $this->dbh->query($sql);
    }
}

// if (empty($_SESSION['auth'])) {
//     header('Location: admin_login.php');
//     exit;
// }

$adminPreResult = new AdminPreResult();
$adminPreResult->changeGameState();
$adminPreResult->judgeWinOrLose();

$numOfPlayers   = $adminPreResult->getNumOfPlayer();
$numOfSurvivors = $adminPreResult->getNumOfSurvivor();

$nowAdminerRps = $adminPreResult->getAdminerChoise();

if ($nowAdminerRps === '1') {
    $radioOne = ' checked';
    $adminerChoise = 'グー';
} elseif ($nowAdminerRps === '2') {
    $radioTwo = ' checked';
    $adminerChoise = 'チョキ';
} elseif ($nowAdminerRps === '3') {
    $radioThree = ' checked';
    $adminerChoise = 'パー';
}

require_once('../html/admin_pre_result.html');
