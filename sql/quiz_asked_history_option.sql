/* 履歴系！！ */
/* 過去に出題されたクイズの選択肢 */
CREATE TABLE quiz_asked_history_option (
    game_id           SMALLINT NOT NULL /* ゲームID 主キー */
    ,game_order       SMALLINT NOT NULL /* そのゲームIDで何問目に出題されたか */
    ,quiz_option_id   VARCHAR(5)        /* 選択肢番号 */
    ,quiz_option_text VARCHAR(100)      /* 選択肢テキスト */
    ,PRIMARY KEY(game_id, game_order, quiz_option_id)
);