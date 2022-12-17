CREATE TABLE quiz_game_member (
    game_id      INT      /* ゲームID */
    ,member_id   INT      /* メンバーID */
    ,member_name CHAR(20) /* メンバー名 */
    ,PRIMARY KEY(game_id, member_id)
);