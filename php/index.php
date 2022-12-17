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

class Index extends DbCommon
{
    /**
     * 残り秒数を取得
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

        $timeLimit = $stmt->fetch(PDO::FETCH_COLUMN);
        if ($timeLimit < 0) {
            $timeLimit = 0;
        }

        return $timeLimit;
    }

    /**
     * ラジオボタンの初期値を設定
     * @return string
     */
    public function getUserRps()
    {
        $sql = 'SELECT rps FROM rock_paper_scissors WHERE member_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$_SESSION['member_id']]);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}

$index = new Index();

$userData  = $index->getUserData($_SESSION['member_id']);
$numOfTime = $index->getNumberOfTimes();

$timeLimit = $index->getTimeLimit();

$numOfSurvivor = $index->getNumOfSurvivor();

$loseFlg = $index->chkLoseFlg();               // 0:勝ち抜き中 1:敗退
if ($loseFlg === '1') {
    require_once('../html/loser.html');
    exit;
}

$nowUserRps = $index->getUserRps();

$radioOne   = '';
$radioTwo   = '';
$radioThree = '';

if ($nowUserRps === '1') {
    $radioOne = ' checked';
} elseif ($nowUserRps === '2') {
    $radioTwo = ' checked';
} elseif ($nowUserRps === '3') {
    $radioThree = ' checked';
}

require_once('../html/index.html');
