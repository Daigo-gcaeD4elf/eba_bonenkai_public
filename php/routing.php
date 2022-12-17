<?php
session_start();

require_once('Common.php');
require_once('Db.php');

if (empty($_SESSION['member_id'])) {

    // if (empty($_POST['member_id'])) {
    //     session_destroy();
    //     header('Location: ../html/login.html');
    //     exit;
    // }

    $_SESSION['member_id'] = $_POST['member_id'];
}

class Routing extends Db
{
    private $pages = [
        '0' => 'explanation',
        '1' => 'index',
        '2' => 'pre_result',
        '3' => 'result',
    ];

    /**
     * 表示するページを取得
     * @return string
     */
    public function judgeRoutingPage()
    {
        $sql = 'SELECT state FROM admin_rock_paper_scissors WHERE admin_member_id = 1';
        $stmt = $this->dbh->query($sql);

        $gameState = $stmt->fetch(PDO::FETCH_COLUMN);
        return $this->pages[$gameState];
    }
}

// print_pre($_SESSION);

$routing = new Routing();
$pageName = $routing->judgeRoutingPage();

require_once($pageName. '.php');
