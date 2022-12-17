<?php
session_start();

require_once('Common.php');
require_once('Db.php');


class Config extends Db
{
    public function changeConfig()
    {
        $sql = <<< EOF
            UPDATE config
            SET
                time_limit = :timeLimit
                ,number_of_winners = :numberOfWinners
                ,draw_judge = :drawJudge
                ,explain_txt = :explainTxt
            WHERE type = 1;
            ;
EOF;
        $stmt = $this->dbh->prepare($sql);

        $executeArray = [
            ':timeLimit' => h($_POST['time_limit']),
            ':numberOfWinners' => h($_POST['number_of_winners']),
            ':drawJudge' => h($_POST['draw_judge']),
            ':explainTxt' => h($_POST['explain_txt']),
        ];

        $stmt->execute($executeArray);

        $sql = <<< EOF
            SELECT
                time_limit
                ,number_of_winners
                ,draw_judge
                ,explain_txt
            FROM
                config
            WHERE
                type = 1
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getConfig()
    {
        $sql = <<< EOF
            SELECT
                time_limit
                ,number_of_winners
                ,draw_judge
                ,explain_txt
            FROM
                config
            WHERE
                type = 1
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
}


// if (empty($_SESSION['auth'])) {
//     header('Location: admin.php');
//     exit;
// }

$config = new Config();

$msg = '';
if (!empty($_POST['change'])) {
    $configInfo = $config->changeConfig();
    $msg = '更新処理が完了しました。';
} else {
    $configInfo = $config->getConfig();
}

$radioZero = '';
$radioOne  = '';

if ($configInfo['draw_judge'] === '0') {
    $radioZero = ' checked';
} elseif ($configInfo['draw_judge'] === '1') {
    $radioOne = ' checked';
}

require_once('../html/config.html');
