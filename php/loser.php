<?php
session_start();

require_once('Common.php');
require_once('Db.php');

if (empty($_SESSION['member_id'])) {

    if (empty($_POST['member_id'])) {
        session_destroy();
        header('Location: ../html/login.html');
        exit;
    }

    $_SESSION['member_id'] = $_POST['member_id'];
}

class Loser extends Db
{
    public function getUserData()
    {
        $sql = 'SELECT * FROM game_member WHERE member_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$_SESSION['member_id']]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTimeLimit()
    {
        $sql = 'SELECT time_limit FROM config WHERE type = 1';
        $stmt = $this->dbh->query($sql);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getNumberOfWins()
    {
        $sql = 'SELECT number_of_wins FROM rock_paper_scissors WHERE member_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$_SESSION['member_id']]);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getNumberOfTimes()
    {
        $sql = 'SELECT number_of_times FROM admin_rock_paper_scissors WHERE admin_member_id = 1';
        $stmt = $this->dbh->query($sql);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getNumOfSurvivor()
    {
        $sql = <<< EOF
            SELECT
                COUNT(GM.member_id) AS member_name
            FROM
                rock_paper_scissors RPS
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = RPS.member_id
            WHERE
                GM.login_state = 1
                AND RPS.lose_flg = 0
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getMemberState()
    {
        $sql = <<< EOF
            SELECT
                A.member_name
                ,B.number_of_wins
            FROM
                game_member A
            LEFT JOIN
                rock_paper_scissors B
            ON
                A.member_id = B.member_id
            WHERE member_id < 15
            ORDER BY B.number_of_wins
EOF;

        $stmt = $this->dbh->query($sql);
        $survivors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

$loser = new Loser();

$numOfWin  = $loser->getNumberOfWins();
$numOfTime = $loser->getNumberOfTimes();

$timeLimit = $loser->getTimeLimit();
$numOfSurvivor = $loser->getNumOfSurvivor();

$members = $loser->getMemberState();
$userData = $loser->getUserData();

require_once('../html/loser.html');
