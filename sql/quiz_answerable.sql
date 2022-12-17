/* 解答権管理用 */
CREATE TABLE quiz_answerble (
    member_id       SMALLINT NOT NULL PRIMARY KEY /* メンバーID 主キー */
    ,answerable_flg TINYINT                       /* 0:解答権なし 1:解答権有り */
    ,loser_flg      TINYINT                       /* 0:生き残っている 1:脱落済 */
);

/*
    UPDATE quiz_answerble
    SET answerablr_flg = 0 or 1
    WHERE member_id = ??
    AND loser_flg = 0
*/