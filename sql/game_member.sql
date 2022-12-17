CREATE TABLE game_member (
    member_id             SERIAL PRIMARY KEY
    ,member_name          VARCHAR(20)
    ,member_name_kana     VARCHAR(50)
    ,mail_address         VARCHAR(50) -- UNIQUE
    ,whether_to_send_mail TINYINT DEFAULT 0
    ,user_id              VARCHAR(20)
    ,password             VARCHAR(2000)
    ,login_state          TINYINT DEFAULT 0
    ,absence_flg          TINYINT DEFAULT 0
    ,retiree_flg          TINYINT DEFAULT 0
    ,staff_flg            TINYINT DEFAULT 0
);