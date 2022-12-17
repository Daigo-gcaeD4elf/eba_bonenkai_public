/* 履歴系！！ */
/* クイズ 回答履歴(管理者側の画面でINSERTすること！！) */
CREATE TABLE quiz_result (
    game_id                    SMALLINT NOT NULL /* ゲームID */
    ,game_order                SMALLINT NOT NULL /* そのゲームID内で何問目か */
    ,member_id                 SMALLINT NOT NULL /* メンバーID */
    ,member_name               VARCHAR(20)       /* メンバー名 */
    ,quiz_id                   INT               /* クイズID */
    ,quiz_question_sentence    VARCHAR(300)      /* クイズ問題文 */
    ,quiz_answer               VARCHAR(100)      /* クイズの答え */
    ,allocation                SMALLINT          /* クイズの配点 */
    ,member_answer_no          SMALLINT NOT NULL /* 回答1 (基本は1のみ。多答問題の場合は2, 3, 4…とレコードが増えていく) */
    ,member_answer             VARCHAR(100)      /* その人の回答 */
    ,is_using_super_satoshikun TINYINT           /* スーパーさとしくん 0:使用してない 1:使用 */
    ,is_using_fifty_fifty      TINYINT           /* フィフティフィフティ 0:使用してない 1:使用 */
    ,is_using_audience         TINYINT           /* オーディエンス 0:使用してない 1:使用 */
    ,required_time             INT               /* 回答に要した時間(ミリ秒でとれる？？) */
    ,is_correct_answer         SMALLINT          /* 正解不正解 0:不正解 1:正解 2:不正解(配点アリ) */
    ,acquired_point            SMALLINT          /* 獲得した点数 */
    ,PRIMARY KEY(game_id, game_order, member_id, member_answer_no)
);

ALTER TABLE quiz_result ADD INDEX index_quiz_result_1 (member_id, game_id);

/*
備忘
多答問題の場合は
GROUP BY game_id, game_order, member_idを行い、
acquired_pointのSUMを行わないことで点数の集計が可能
*/

/*2022忘年会*/
-- ALTER TABLE quiz_result ADD is_using_fifty_fifty TINYINT DEFAULT 0 AFTER is_using_super_satoshikun;
-- ALTER TABLE quiz_result ADD is_using_audience TINYINT DEFAULT 0 AFTER is_using_fifty_fifty;
