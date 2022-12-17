-- ログインユーザー確認用
SELECT * FROM game_member WHERE login_state = 1;
SELECT COUNT(member_id) FROM game_member WHERE login_state = 1;

-- ログインしていないユーザーはこっち
SELECT * FROM game_member WHERE login_state <> 1;

-- クイズ→じゃんけんへ移行する際にやっておく
UPDATE admin_rock_paper_scissors SET state = 0;
UPDATE rock_paper_scissors SET rps = 1, number_of_wins = 0, lose_flg = 0;
