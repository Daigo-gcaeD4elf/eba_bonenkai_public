<?php
session_start();

require_once('Common.php');
require_once('Db.php');

class AdminLogin extends Db
{
    /**
     * パスワードチェック
     * @return bool
     */
    public function chkUserPass()
    {
        $sql = 'SELECT admin_member_pass FROM admin_member WHERE admin_member_name = :name';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([':name' => $_POST['username']]);

        $dbPass = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return password_verify($_POST['pass'], $dbPass[0]);
    }

    /**
     * ユーザー存在チェック 管理者は1ユーザーしか登録不可
     * @return bool
     */
    public function chkUserExist()
    {
        $sql = 'SELECT COUNT(*) FROM admin_member';
        $stmt = $this->dbh->query($sql);
        $userExist = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return ($userExist[0] > 0);
    }

    /**
     * ユーザー登録 管理者は1ユーザーしか登録不可
     * @return void
     */
    public function setUser()
    {
        $sql = <<< EOF
            INSERT INTO admin_member (
                admin_member_name
                ,admin_member_pass
            ) VALUES (
                :name
                ,:pass
            );
EOF;
        $stmt = $this->dbh->prepare($sql);
        $hash = password_hash($_POST['pass'], PASSWORD_BCRYPT);
        $stmt->execute([':name' => $_POST['username'], ':pass' => $hash]);
    }
}

if (!empty($_SESSION['auth'])) {
	session_destroy();
}

$adminLogin = new AdminLogin();

// ログインボタン
if (!empty($_POST['send'])) {
    if ($adminLogin->chkUserPass()) {
        $_SESSION['auth'] = 1;
        header('Location: admin.php');
        exit;
    }
    echo 'ユーザー名とパスワードが合致しません。';
}

// 新規登録
if (!empty($_POST['reg'])) {
    if ($adminLogin->chkUserExist()) {
        echo '既に登録されているデータが存在します';
    } else {
        $adminLogin->setUser();
        echo '登録しました。';
    }
}

require_once('../html/admin_login.html');
