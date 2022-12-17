/* 選択問題の選択肢 */
CREATE TABLE quiz_option (
    quiz_id              SMALLINT NOT NULL   /* クイズID */
    ,quiz_option_id      VARCHAR(5)          /* 選択肢 ID */
    ,quiz_option_text    VARCHAR(100)        /* 選択肢 */
    ,fifty_fifty_display CHAR(1) DEFAULT '1' /* 50-50押下時に選択可かどうか 0:選択不可・ */
    ,PRIMARY KEY(quiz_id, quiz_option_id)
);

/* 2022年用に追加 */
-- ALTER TABLE quiz_option ADD fifty_fifty_display CHAR(1) DEFAULT '1' AFTER quiz_option_text;