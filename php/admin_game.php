<?php
session_start();

require_once('Common.php');
require_once('Db.php');
require_once('DbCommon.php');

class AdminGame extends DbCommon
{
    /**
     * 制限時間取得
     * @return string
     */
    public function getTimeLimit()
    {
        $sql = <<< EOF
        SELECT
            TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(renewal_time, INTERVAL (
                SELECT
                    time_limit
                FROM
                    config
                WHERE
                    type = 1
            ) SECOND)) AS time_limit
        FROM
            admin_rock_paper_scissors
        WHERE
            admin_member_id = 1
EOF;
        $stmt = $this->dbh->query($sql);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}

// if (empty($_SESSION['auth'])) {
//     header('Location: admin_login.php');
//     exit;
// }

$adminGame = new AdminGame();

$nowAdminerRps = $adminGame->getAdminerChoise();
$timeLimit = $adminGame->getTimeLimit();

$numOfPlayers   = $adminGame->getNumOfPlayer();
$numOfSurvivors = $adminGame->getNumOfSurvivor();

$numOfTime = $adminGame->getNumberOfTimes();

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

require_once('../html/admin_game.html');
