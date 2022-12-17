<?php
session_start();

require_once('Common.php');
require_once('Db.php');
require_once('DbCommon.php');

class Admin extends DbCommon
{
    /**
     * ゲームスタート
     * @return void
     */
    public function startGame()
    {
        $sql = <<< EOF
            UPDATE admin_rock_paper_scissors
            SET
                renewal_time = NOW()
                ,state = 1
                ,number_of_times = number_of_times + 1
            WHERE
                admin_member_id = 1
            ;
EOF;

        $stmt = $this->dbh->query($sql);
    }
}

$admin = new Admin();

// 次のじゃんけんへ
if (!empty($_POST['game_start'])) {
    $admin->startGame();
    header('Location: ./admin_game.php');
    exit;
}

// if (empty($_SESSION['auth'])) {
//     header('Location: admin_login.php');
//     exit;
// }

require_once('../html/admin.html');
