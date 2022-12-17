<?php
session_start();

require_once('Common.php');
require_once('Db.php');
require_once('DbCommon.php');

if (empty($_SESSION['member_id'])) {

    if (empty($_POST['member_id'])) {
        session_destroy();
        header('Location: ../html/login.html');
        exit;
    }

    $_SESSION['member_id'] = $_POST['member_id'];
}

class Result extends DbCommon
{
    /**
     * 勝者一覧を取得
     * @return array
     */
    public function getWinner($adminerRps)
    {
        $winnerRps = '';
        if ($adminerRps === '1') {
            $winnerRps = '3';
        } elseif ($adminerRps === '2') {
            $winnerRps = '1';
        } elseif ($adminerRps === '3') {
            $winnerRps = '2';
        }

        $sql = <<< EOF
            SELECT
                GM.member_name AS member_name
            FROM
                rock_paper_scissors RPS
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = RPS.member_id
            WHERE
                GM.login_state = 1
                AND RPS.lose_flg = 0
                AND RPS.rps = {$winnerRps}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 引き分けたメンバー一覧を取得
     * @return array
     */
    public function getDrawer($adminerRps)
    {
        $sql = 'SELECT draw_judge FROM config LIMIT 1';
        $stmt = $this->dbh->query($sql);

        $drawJudge = $stmt->fetch(PDO::FETCH_COLUMN);

        $drawerRps = $adminerRps;

        $sql = <<< EOF
            SELECT
                GM.member_name AS member_name
            FROM
                rock_paper_scissors RPS
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = RPS.member_id
            WHERE
                GM.login_state = 1
                AND RPS.lose_flg = 0
                AND RPS.rps = {$drawerRps}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 敗者一覧を取得
     * @return array
     */
    public function getLoser($adminerRps)
    {
        $loserRps = '';
        if ($adminerRps === '1') {
            $loserRps = '2';
        } elseif ($adminerRps === '2') {
            $loserRps = '3';
        } elseif ($adminerRps === '3') {
            $loserRps = '1';
        }

        $sql = <<< EOF
            SELECT
                GM.member_name AS member_name
            FROM
                rock_paper_scissors RPS
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = RPS.member_id
            WHERE
                GM.login_state = 1
                AND RPS.lose_flg = 0
                AND RPS.rps = {$loserRps}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$result = new Result();

$userData = $result->getUserData($_SESSION['member_id']);

$numOfPlayers   = $result->getNumOfPlayer();
$numOfSurvivors = $result->getNumOfSurvivor();

// $adminerRps = $result->getAdminerRps();
// $winners = $result->getWinner($adminerRps);
// $drawers = $result->getDrawer($adminerRps);
// $losers  = $result->getLoser($adminerRps);

$survivors = $result->getSurvivor();
$numOfTimes = $result->getNumberOfTimes();

require_once('../html/result.html');
