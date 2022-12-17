CREATE TABLE quiz_admin_status (
    admin_member_id               SMALLINT NOT NULL PRIMARY KEY
    ,now_state                    SMALLINT /* 0:ゲーム中ではない 1:回答画面 2:回答待ち 3:結果発表・次のクイズ待ち */
    ,renewal_time                 DATETIME(3)
    ,now_game_id                  SMALLINT
    ,now_game_order               SMALLINT
    ,now_quiz_id                  SMALLINT
    ,now_playlist_id              SMALLINT
    ,now_playlist_order           SMALLINT
    ,super_satoshikun_power_point SMALLINT /* ユーザー画面ではプレイリスト系テーブルを見ないため */
    ,fifty_fifty_power_point      SMALLINT /* ユーザー画面ではプレイリスト系テーブルを見ないため */
    ,audience_power_point         SMALLINT /* ユーザー画面ではプレイリスト系テーブルを見ないため */
);

/* 2022忘年会追加 */
-- ALTER TABLE quiz_admin_status ADD fifty_fifty_power_point SMALLINT DEFAULT 1 AFTER super_satoshikun_power_point;
-- ALTER TABLE quiz_admin_status ADD audience_power_point SMALLINT DEFAULT 1 AFTER fifty_fifty_power_point;
