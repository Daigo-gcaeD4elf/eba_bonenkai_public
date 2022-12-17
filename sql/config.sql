CREATE TABLE config (
    type               INT PRIMARY KEY          /* 1 : じゃんけんゲーム */
    ,time_limit        INT                      /* 選択時の制限時間 */
    ,number_of_winners INT                      /* 勝利人数設定 */
    ,draw_judge        TINYINT                  /* 相子の場合の判定 0:勝ち、1:負け */
    ,explain_txt       VARCHAR(2500) DEFAULT '' /* ゲーム説明テキスト */
);

INSERT INTO config (
    type
    ,time_limit
    ,number_of_winners
    ,draw_judge
) VALUES (
    1
    ,20
    ,5
    ,1
);