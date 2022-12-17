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

class PreResult extends DbCommon
{
    /**
     * ユーザーの選択(グーorチョキorパー)を取得
     * @param string $userId ユーザーID
     * @return string
     */
    public function getUserChoise($userId)
    {
        $sql = 'SELECT rps FROM rock_paper_scissors WHERE member_id = '. $userId;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 勝敗判定
     * @return void
     */
    public function judge($yourChoise, $adminerChoise, $drawJudge)
    {
        $winOrLose = 0;
        // 勝敗判定
        if (
                $yourChoise === '1' && $adminerChoise === '2' // 自分:グー   社長:チョキ
            ||  $yourChoise === '2' && $adminerChoise === '3' // 自分:チョキ 社長:パー
            ||  $yourChoise === '3' && $adminerChoise === '1' // 自分:パー   社長:グー
        ) {
            $winOrLose = 1;
        }

        // アイコは勝ちの場合
        if ($drawJudge === '0') {
            if ($yourChoise === $adminerChoise) {
                $winOrLose = 1;
            }
        }

        return $winOrLose;
    }
}

$preResult = new PreResult();

$userData = $preResult->getUserData($_SESSION['member_id']);

$yourChoise    = $preResult->getUserChoise($_SESSION['member_id']);
$adminerChoise = $preResult->getAdminerChoise();

$numOfTime = $preResult->getNumberOfTimes();

// 既に負けている場合は敗者用の画面へ (DB値で判断すると、今回負けた人もloserページに飛んでしまう)
// $loseFlg = $preResult->chkLoseFlg();
if (!empty($_POST['lose_flg'])) {
    require_once('../html/loser_pre_result.html');
    exit;
}

$fmtYourChoise    = $rpsName[$yourChoise];
$fmtAdminerChoise = $rpsName[$adminerChoise];

$drawJudge = $preResult->getMode();

$winOrLose = 0;
if ($yourChoise !== 0) {
    $winOrLose = $preResult->judge($yourChoise, $adminerChoise, $drawJudge);  //勝敗判定フラグ  0:負け  1:勝ち
}

require_once('../html/pre_result.html');
