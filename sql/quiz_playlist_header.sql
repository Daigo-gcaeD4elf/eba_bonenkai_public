CREATE TABLE quiz_playlist_header (
    playlist_id                   INT PRIMARY KEY    /* 主キー */
    ,playlist_title               VARCHAR(50)        /* プレイリスト名 */
    ,super_satoshikun_power_point SMALLINT DEFAULT 2 /* スーパーさとしくん使用可能回数 */
    ,fifty_fifty_power_point      SMALLINT DEFAULT 1 /* フィフティフィフティ使用可能回数 */
    ,audience_power_point         SMALLINT DEFAULT 1 /* オーディエンス使用可能回数 */
);

/* 2022年用に追加 */
-- ALTER TABLE quiz_playlist_header ADD fifty_fifty_power_point SMALLINT DEFAULT 1 AFTER super_satoshikun_power_point;
-- ALTER TABLE quiz_playlist_header ADD audience_power_point SMALLINT DEFAULT 1 AFTER fifty_fifty_power_point;
