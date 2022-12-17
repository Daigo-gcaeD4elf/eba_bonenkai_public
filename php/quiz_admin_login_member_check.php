<?php
session_start();

require_once('Common.php');
require_once('Db.php');

if (!empty($_SESSION['auth'])) {
	session_destroy();
}

class QuizAdminLoginMemberCheck extends Db
{
    /**
     * メンバーのログイン予定数を取得
     *
     * @return array
     */
    public function fetchLoginMembers()
    {
        $sql = <<< EOF
            SELECT
                COUNT(1) AS num_of_all_members
                ,SUM(IF(absence_flg = '0' AND staff_flg = '0', 1, 0)) AS num_of_participation_members
                ,SUM(IF(absence_flg = '1', 1, 0)) AS num_of_abcence_members
                ,SUM(IF(staff_flg = '1', 1, 0)) AS num_of_staff_members
            FROM
                game_member
            WHERE
                retiree_flg = '0'
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 欠席連絡済のメンバーを取得
     *
     * @return array
     */
    public function fetchAbsenceMembersNameList()
    {
        $sql = <<< EOF
            SELECT
                member_name
            FROM
                game_member
            WHERE
                retiree_flg = '0'
            AND absence_flg = '1'
            ORDER BY
                member_name_kana ASC
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 欠席連絡済のメンバーを取得
     *
     * @return array
     */
    public function fetchStaffMembersNameList()
    {
        $sql = <<< EOF
            SELECT
                member_name
            FROM
                game_member
            WHERE
                retiree_flg = '0'
            AND staff_flg = '1'
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}

$quizAdminLoginMemberCheck = new QuizAdminLoginMemberCheck();
$member = [];
$absenceMembersNameList = [];
$staffMembersNameList = [];
try {
    $member = $quizAdminLoginMemberCheck->fetchLoginMembers();
    $absenceMembersNameList = $quizAdminLoginMemberCheck->fetchAbsenceMembersNameList();
    $staffMembersNameList = $quizAdminLoginMemberCheck->fetchStaffMembersNameList();
} catch (Exception $e) {
    writeErrorLog(__FILE__, __FUNCTION__, __LINE__, $e->getMessage());
}

require_once('../html/quiz_admin_login_member_check.html');
