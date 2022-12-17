CREATE TABLE login_error (
    login_error_id     SERIAL PRIMARY KEY
    ,entry_time        DATETIME
    ,inputted_id       VARCHAR(20)
    ,inputted_password VARCHAR(20)
    ,ip_address        VARCHAR(50)
);