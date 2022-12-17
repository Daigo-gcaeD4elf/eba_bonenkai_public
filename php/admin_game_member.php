<?php
session_start();

require_once('Common.php');
require_once('Db.php');

class AdminGameMember extends Db
{
    public function getGameMember()
    {
        $sql = <<< EOF
            SELECT
                member_name
                ,user_id
                ,password
                ,mail_address
                ,CASE
                    WHEN whether_to_send_mail = 1 THEN '〇'
                    ELSE ''
                END AS whether_to_send_mail
                ,CASE
                    WHEN login_state = '1' THEN 'ログイン中'
                    ELSE ''
                END AS login_state
            FROM
                game_member
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addGameMember($memberName, $mailAddress, $userId, $password)
    {
        $sql = <<< EOF
            INSERT INTO game_member(
                member_name
                ,mail_address
                ,user_id
                ,password
            ) VALUES (
                '{$memberName}'
                ,'{$mailAddress}'
                ,'{$userId}'
                ,'{$password}'
            )
        ;
EOF;
        $stmt = $this->dbh->query($sql);

        $sql = <<< EOF
            INSERT INTO rock_paper_scissors(
                rps
                ,number_of_wins
            ) VALUES (
                1
                ,0
            )
EOF;

        $stmt = $this->dbh->query($sql);
        return 'メンバーを追加しました。';
    }

    public function sendMail()
    {
        mb_language("ja");
        mb_internal_encoding("UTF-8");

        $sql = <<< EOF
            SELECT
                member_name
                ,user_id
                ,password
                ,mail_address
            FROM
                game_member
            WHERE
                whether_to_send_mail = 1
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mailTitle = '2020年度 EBA(株) 忘年会 じゃんけんげーむログイン情報';

        $from = mb_encode_mimeheader('ebabonekai2020');
        $fromMailAddress = 'ebabonekai2020';
        $fromName = 'noreply';

        $mailHeader .= "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\n";
        $mailHeader .= 'From:'. $from. "\n";
        // $mailHeader .= 'Reply-To:'. $fromMailAddress. "\n";
        $mailHeader .= 'Organization:'. $fromName. "\n";

        $file_path = "../doc/ebabonenkaiqr.png";
        $file = "ebabonenkaiqr.png";

        $sendErr = '';
        $text = 'てすと送信';
        foreach ($members as $member) {
            $mailTo = $member['mail_address'];
            $mailMessage = $member['member_name']. " さん。". "\n\n";
            $mailMessage .= "2020年忘年会、じゃんけんゲームを始めます。". "\n";
            $mailMessage .= "以下のURL、または添付のQRコードよりサイトにアクセスし、ユーザーIDとパスワードを入力してログインして下さい。". "\n\n";
            $mailMessage .= "http://multi-sites.info/bonenkai/html/login.html". "\n\n";
            $mailMessage .= "ユーザーID : ". $member['user_id']. "\n";
            $mailMessage .= "パスワード : ". $member['password']. "\n\n";
            $mailMessage .= "※こちらのアドレスは送信専用です。";

            $body = "--__BOUNDARY__\n";
            $body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
            $body .= $mailMessage . "\n";
            $body .= "--__BOUNDARY__\n";

            $body .= "Content-Type: application/octet-stream; name=\"{$file}\"\n";
            $body .= "Content-Disposition: attachment; filename=\"{$file}\"\n";
            $body .= "Content-Transfer-Encoding: base64\n";
            $body .= "\n";
            $body .= chunk_split(base64_encode(file_get_contents($file_path)));
            $body .= "--__BOUNDARY__\n";

            if (!mb_send_mail($mailTo, $mailTitle, $body, $mailHeader)) {
                $sendErr .= $member['member_name']. 'さん、';
            }
        }

        $msg = 'メールを送信しました。';
        if ($sendErr !== '') {
            $msg = $sendErr. 'にメールを送ることができませんでした。';
        }

        return $msg;
    }

}

// if (empty($_SESSION['auth'])) {
//     header('Location: admin_login.php');
//     exit;
// }

$adminGameMember = new AdminGameMember();

$msg = '';
// メンバー追加
if (!empty($_POST['addMember'])) {
    $adminGameMember->addGameMember($_POST['newMemberName'], $_POST['newMailAddress'], $_POST['newUserId'], $_POST['newPassword']);
}

// メール送信
// if (!empty($_POST['sendMail'])) {
//     $msg = $adminGameMember->sendMail();
// }

$members = $adminGameMember->getGameMember();

require_once('../html/admin_game_member.html');
