-- ログインユーザー確認用
SELECT * FROM game_member WHERE login_state = 2;
SELECT COUNT(member_id) FROM game_member WHERE login_state = 2;

-- ログインしていないユーザーはこっち
SELECT * FROM game_member WHERE login_state <> 2;
SELECT * FROM game_member WHERE login_state = 0;
SELECT * FROM game_member WHERE login_state = 5;

-- クイズ前に行う
DELETE FROM quiz_admin_status;
TRUNCATE TABLE login_error;

-- テストでできた履歴系テーブルの削除
TRUNCATE TABLE quiz_asked_history;
TRUNCATE TABLE quiz_asked_history_option;
TRUNCATE TABLE quiz_game_history;
TRUNCATE TABLE quiz_game_member;
TRUNCATE TABLE quiz_result;

-- メンバーをログアウトさせる
UPDATE game_member SET login_state = 0 WHERE login_state <> 5;

-- ログインエラーしてるメンバーのチェック
SELECT * FROM login_error ORDER BY entry_time DESC;

SELECT * FROM game_member WHERE member_id = ;

UPDATE game_member SET absence_flg = 1 WHERE user_id = '';