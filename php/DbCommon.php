<?php
require_once('Common.php');
require_once('Db.php');

class DbCommon extends Db
{
    /**
     * ユーザー情報(名前等)取得
     * @param string $memberId メンバーID
     * @return array
    */
    public function getUserData($memberId)
    {
        $sql = 'SELECT * FROM game_member WHERE member_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$_SESSION['member_id']]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ???
     */
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
            WHERE A.member_id < 15
            ORDER BY B.number_of_wins
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ゲーム参加者数取得
     * @return string
     */
    public function getNumOfPlayer()
    {
        $sql = 'SELECT count(member_id) FROM game_member WHERE login_state = 1';
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 生き残り人数取得
     * @return string
     */
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

    /**
     * 第n回戦のnを取得
     * @return string
     */
    public function getNumberOfTimes()
    {
        $sql = 'SELECT number_of_times FROM admin_rock_paper_scissors WHERE admin_member_id = 1';
        $stmt = $this->dbh->query($sql);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 勝ち抜き中かどうかチェック
     * @return string
     */
    public function chkLoseFlg()
    {
        $sql = 'SELECT lose_flg FROM rock_paper_scissors WHERE member_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$_SESSION['member_id']]);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * アイコの場合の判定方法取得
     * @return string
     */
    public function getMode()
    {
        $sql = 'SELECT draw_judge FROM config LIMIT 1';
        $stmt = $this->dbh->query($sql);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 生き残りメンバー一覧を取得
     * @return array
     */
    public function getSurvivor()
    {
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
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 管理者の選択(グーorチョキorパー)を取得
     * @return string
     */
    public function getAdminerChoise()
    {
        $sql = 'SELECT admin_choise FROM admin_rock_paper_scissors WHERE admin_member_id = 1';
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}
