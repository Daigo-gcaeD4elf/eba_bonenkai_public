/* 履歴系！！ */
CREATE TABLE quiz_game_history (
    game_id      SERIAL PRIMARY KEY /* ゲームID 主キー */
    ,game_date   DATETIME           /* ゲーム開始時刻 */
    ,game_name   VARCHAR(100)       /* ゲーム名 */
    ,is_survival TINYINT            /* 0:サバイバル形式でない 1:サバイバル */
);