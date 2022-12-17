CREATE TABLE quiz_playlist (
    playlist_id     SMALLINT /* プレイリスト番号 */
    ,playlist_order SMALLINT /* 順番 */
    ,quiz_id        SMALLINT /* quizテーブルのid */
    ,PRIMARY KEY(playlist_id, playlist_order)
);

/*
備忘
UPDATE quiz_playlist
SET quiz_order = quiz_order + 1
WHERE id <> 編集中のid
AND quiz_order BETWEEN 編集中idの変更後order AND 編集中idの元order

→ DELETE INSERTで良いかも？？
*/