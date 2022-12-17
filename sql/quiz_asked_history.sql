/* 履歴系！！ 但し更新必要  */
/* 過去に出題されたクイズ */
CREATE TABLE quiz_asked_history (
    game_id                 SMALLINT NOT NULL /* ゲームID 主キー */
    ,game_order             SMALLINT NOT NULL /* そのゲームIDで何問目に出題されたか */
    ,quiz_start_time        DATETIME(3)       /* クイズ出題時刻(ミリ秒まで) */
    ,quiz_question_sentence VARCHAR(300)      /* クイズの問題文 */
    ,quiz_answer            VARCHAR(100)      /* クイズの答え */
    ,allocation             SMALLINT          /* 配点 */
    ,PRIMARY KEY(game_id, game_order)
);

/* 出題時にINSERT 回答登録時にUPDATE */