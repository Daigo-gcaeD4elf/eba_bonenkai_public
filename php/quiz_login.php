<?php
session_start();

require_once('Common.php');
require_once('Db.php');

class Login extends Db
{
    /**
     * パスワードチェック
     * @return array
     */
    public function chkUserPass()
    {
        $sql = <<< EOF
            SELECT
                member_id
                ,member_name
            FROM
                game_member
            WHERE
                user_id = :id
            AND password = :pass
EOF;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([':id' => $_POST['id'], ':pass' => $_POST['password']]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ログイン状態にする
     * @return void
     */
    public function updateLoginState($memberId)
    {
        $sql = <<< EOF
            UPDATE
                game_member
            SET
                login_state = 2
            WHERE
                member_id = :id
EOF;
        $stmt =  $this->dbh->prepare($sql);
        $stmt->execute([':id' => $memberId]);
    }

    /**
     * ログイン失敗履歴を残す
     * @return void
     */
    public function resisterLoginError()
    {
        $sql = <<< EOF
            INSERT INTO login_error (
                entry_time
                ,inputted_id
                ,inputted_password
                ,ip_address
            ) VALUES (
                NOW()
                ,:id
                ,:password
                ,:ip_address
            );
EOF;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([':id' => mb_substr($_POST['id'], 0, 20), ':password' => mb_substr($_POST['password'], 0, 20), ':ip_address' => mb_substr($_SERVER['REMOTE_ADDR'], 0, 50)]);
    }
}

$login = new Login();

$loginErr = '';
// ログインボタン押下

try {
    if (!empty($_POST['send'])) {

        $userInfo = $login->chkUserPass();

        if (!empty($userInfo)) {
            $_SESSION['member_id']   = $userInfo['member_id'];
            $_SESSION['member_name'] = $userInfo['member_name'];

            $login->updateLoginState($userInfo['member_id']);
            header('Location: eba_quiz.php');
            exit;
        }

        $login->resisterLoginError();
        $loginErr = 'ユーザー名とパスワードが一致しません';
    }
} catch (Exception $e) {
    print_pre($e->getMessage());
}

session_destroy();

require_once('../html/quiz_login.html');
