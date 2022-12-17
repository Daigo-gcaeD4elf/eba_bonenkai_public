<?php
require_once('Common.php');
require_once('Db.php');
require_once('quiz_status.php');

class QuizFunc extends Db
{
    // デバッグ用・・・
    public function debugLog($str)
    {
        $sql = <<< EOF
            INSERT INTO login_error (
                entry_time
                ,inputted_id
                ,inputted_password
            ) VALUES (
                NOW()
                ,'game_start'
                ,'{$str}'
            );
EOF;
        $this->dbh->query($sql);
    }

    /**
     * ユーザー情報取得
     * @param string $memberId
     * @return array
     */
    public function fetchMemberData($memberId)
    {
        $sql = 'SELECT * FROM game_member WHERE member_id = :id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute(['id' => $memberId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // FIXME:ユーザー画面で使うSQLはこの辺りに

    /**
     * 現在の回答状況を取得(初回or画面更新時)
     * @param array $adminStatus
     * @param int $memberId
     * @return string
     */
    public function fetchUserAnswer($adminStatus, $memberId)
    {
        $gameId = $adminStatus['now_game_id'];
        $gameOrder = $adminStatus['now_game_order'];

        $sql = <<< EOF
            SELECT
                member_answer
                ,is_using_super_satoshikun
                ,is_using_fifty_fifty
                ,is_using_audience
            FROM
                quiz_member_answer
            WHERE
                member_id = {$memberId}
            AND game_id = {$gameId}
            AND game_order = {$gameOrder}
        ;
EOF;
        $stmt = $this->dbh->query($sql);
        $defaultAnswer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($defaultAnswer)) {
            $defaultAnswer = '1';
        }
        return $defaultAnswer;
    }

    /**
     * スーパーさとしくんの残機取得
     * @param array $nowQuizAdminStatus
     * @param int $memberId
     * @return int
     */
    public function fetchSuperSatoshikunStock($nowQuizAdminStatus, $memberId)
    {
        $gameId    = $nowQuizAdminStatus['now_game_id'];
        $gameOrder = $nowQuizAdminStatus['now_game_order'];

        $usingSuperSatoshikun = USING_SUPER_SATOSHIKUN;

        // スーパーさとしくん使用済回数を取得
        $sql = <<< EOF
            SELECT
                COUNT(is_using_super_satoshikun)
            FROM
                quiz_member_answer
            WHERE
                member_id = {$memberId}
            AND game_id = {$gameId}
            AND game_order < {$gameOrder}
            AND is_using_super_satoshikun = {$usingSuperSatoshikun}
EOF;
        $stmt = $this->dbh->query($sql);
        $usedSuperSatoshikunCnt = $stmt->fetch(PDO::FETCH_COLUMN);

        $superSatoshikunStock = $nowQuizAdminStatus['super_satoshikun_power_point'] - $usedSuperSatoshikunCnt;
        if ($superSatoshikunStock < 0) {
            $superSatoshikunStock = 0;
        }

        return $superSatoshikunStock;
    }

    /**
     * ライフライン(50-50, オーディエンス)の各種残機取得
     * @param array $nowQuizAdminStatus
     * @param int $memberId
     * @return array
     */
    public function fetchLifeLineStock($nowQuizAdminStatus, $memberId)
    {
        $gameId    = $nowQuizAdminStatus['now_game_id'];
        $gameOrder = $nowQuizAdminStatus['now_game_order'];

        $usingLifeLine = USING_LIFE_LINE;

        // ライフライン使用済回数を取得
        $sql = <<< EOF
            SELECT
                {$nowQuizAdminStatus['fifty_fifty_power_point']} - SUM(IF(is_using_fifty_fifty = {$usingLifeLine}, 1, 0)) AS fifty_fifty_stock
                ,{$nowQuizAdminStatus['audience_power_point']} - SUM(IF(is_using_audience = {$usingLifeLine}, 1, 0)) AS audience_stock
            FROM
                quiz_member_answer
            WHERE
                member_id = {$memberId}
            AND game_id = {$gameId}
            AND game_order <= {$gameOrder}
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * フィフティフィフティを使用
     * @param array $nowQuizAdminStatus
     * @param int $memberId
     * @return void
     */
    public function useFiftyFifty($nowQuizAdminStatus, $memberId)
    {
        $gameId    = $nowQuizAdminStatus['now_game_id'];
        $gameOrder = $nowQuizAdminStatus['now_game_order'];

        $usingLifeLine = USING_LIFE_LINE;

        $sql = <<< EOF
            UPDATE quiz_member_answer
            SET
                is_using_fifty_fifty = '{$usingLifeLine}'
            WHERE
                member_id  = {$memberId}
            AND game_id    = {$gameId}
            AND game_order = {$gameOrder}
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * オーディエンスを使用
     * @param array $nowQuizAdminStatus
     * @param int $memberId
     * @return void
     */
    public function useAudience($nowQuizAdminStatus, $memberId)
    {
        $gameId    = $nowQuizAdminStatus['now_game_id'];
        $gameOrder = $nowQuizAdminStatus['now_game_order'];

        $usingLifeLine = USING_LIFE_LINE;

        $sql = <<< EOF
            UPDATE quiz_member_answer
            SET
                is_using_audience = '{$usingLifeLine}'
            WHERE
                member_id  = {$memberId}
            AND game_id    = {$gameId}
            AND game_order = {$gameOrder}
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * これまで獲得したポイントを取得
     * @param int $gameId
     * @param int $memberId
     */
    public function getTotalPoint($gameId, $memberId)
    {
        $sql = <<< EOF
            SELECT
                SUM(acquired_point) AS total_point
            FROM
                quiz_member_answer
            WHERE
                member_id = {$memberId}
            AND game_id = {$gameId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * これまで獲得したポイントを取得(履歴テーブルから)
     * @param int $gameId
     * @param int $memberId
     */
    public function getTotalPointFromResultTable($gameId, $memberId)
    {
        $sql = <<< EOF
            SELECT
                SUM(acquired_point) AS total_point
            FROM
                quiz_result
            WHERE
                member_id = {$memberId}
            AND game_id = {$gameId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * これまでのクイズの結果(ユーザー単位) ※獲得ポイントの詳細もここから
     * @param int $gameId
     * @param int $memberId
     * @return array
     */
    public function getMemberResult($gameId, $memberId)
    {
        $sql = <<< EOF
            SELECT
                QR.game_order
                ,QAH.quiz_question_sentence
                ,QR.member_answer
                ,QAHO1.quiz_option_text AS member_answer_text
                ,QAH.quiz_answer
                ,QAHO2.quiz_option_text AS quiz_answer_text
                ,QR.is_correct_answer
                ,QAH.allocation
                ,QR.is_using_super_satoshikun
                ,QR.acquired_point
            FROM
                quiz_result QR
            LEFT JOIN
                quiz_asked_history QAH
            ON
                QAH.game_id = QR.game_id
            AND QAH.game_order = QR.game_order
            LEFT JOIN
                quiz_asked_history_option QAHO1
            ON
                QAHO1.game_id = QR.game_id
            AND QAHO1.game_order = QR.game_order
            AND QAHO1.quiz_option_id = QR.member_answer
            LEFT JOIN
                quiz_asked_history_option QAHO2
            ON
                QAHO2.game_id = QR.game_id
            AND QAHO2.game_order = QR.game_order
            AND QAHO2.quiz_option_id = QAH.quiz_answer
            WHERE
                QR.member_id = {$memberId}
            AND QR.game_id = {$gameId}
            ORDER BY
                QR.game_order
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ユーザーの順位取得
     * @param int $gameId
     * @param int $memberId
     * @return array
     */
    public function getMemberTotalScore($gameId, $memberId)
    {
        $sql = <<< EOF
            SELECT
                total_point
                ,(
                    SELECT
                        COUNT(*) + 1
                    FROM
                        (
                            SELECT
                                SUM(acquired_point) AS total_point
                            FROM
                                quiz_member_answer
                            WHERE
                                game_id = {$gameId}
                            GROUP BY
                                member_id
                        ) B
                    WHERE
                        B.total_point > A.total_point
                ) AS ranking
            FROM
            (
                SELECT
                    member_id
                    ,SUM(acquired_point) AS total_point
                FROM
                    quiz_member_answer
                WHERE
                    game_id = {$gameId}
                GROUP BY
                    member_id
            ) A
            WHERE
                member_id = {$memberId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ユーザーの順位取得
     * @param int $gameId
     * @param int $memberId
     * @return array
     */
    public function getMemberTotalScoreFromResultTable($gameId, $memberId)
    {
        $sql = <<< EOF
            SELECT
                total_point
                ,(
                    SELECT
                        COUNT(*) + 1
                    FROM
                        (
                            SELECT
                                SUM(acquired_point) AS total_point
                            FROM
                                quiz_result
                            WHERE
                                game_id = {$gameId}
                            GROUP BY
                                member_id
                        ) B
                    WHERE
                        B.total_point > A.total_point
                ) AS ranking
            FROM
            (
                SELECT
                    member_id
                    ,SUM(acquired_point) AS total_point
                FROM
                    quiz_result
                WHERE
                    game_id = {$gameId}
                GROUP BY
                    member_id
            ) A
            WHERE
                member_id = {$memberId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 全体の順位取得
     * @param int $gameId
     * @param int $maxFetchRanking
     * @return array
     */
    public function getTotalScore($gameId, $maxFetchRanking = 0)
    {
        $havingRanking = '';
        if ($maxFetchRanking > 0) {
            $havingRanking = ' HAVING ranking <= '. $maxFetchRanking;
        }

        $sql = <<< EOF
            SELECT
                A.member_id
                ,GM.member_name
                ,A.total_point
                ,(
                    SELECT
                        COUNT(*) + 1
                    FROM
                        (
                            SELECT
                                SUM(acquired_point) AS total_point
                            FROM
                                quiz_member_answer
                            WHERE
                                game_id = {$gameId}
                            GROUP BY
                                member_id
                        ) B
                    WHERE
                        B.total_point > A.total_point
                ) AS ranking
            FROM
            (
                SELECT
                    member_id
                    ,SUM(acquired_point) AS total_point
                FROM
                    quiz_member_answer
                WHERE
                    game_id = {$gameId}
                GROUP BY
                    member_id
            ) A
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = A.member_id
            {$havingRanking}
            ORDER BY
                ranking ASC
                ,member_id ASC
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * 全体の順位取得(履歴テーブルから)
     * @param int $gameId
     * @param int $maxFetchRanking
     * @return array
     */
    public function getTotalScoreFromResultTable($gameId, $maxFetchRanking = 0)
    {
        $havingRanking = '';
        if ($maxFetchRanking > 0) {
            $havingRanking = ' HAVING ranking <= '. $maxFetchRanking;
        }

        $sql = <<< EOF
            SELECT
                A.member_id
                ,GM.member_name
                ,A.total_point
                ,(
                    SELECT
                        COUNT(*) + 1
                    FROM
                        (
                            SELECT
                                SUM(acquired_point) AS total_point
                            FROM
                                quiz_result
                            WHERE
                                game_id = {$gameId}
                            GROUP BY
                                member_id
                        ) B
                    WHERE
                        B.total_point > A.total_point
                ) AS ranking
            FROM
            (
                SELECT
                    member_id
                    ,SUM(acquired_point) AS total_point
                FROM
                    quiz_result
                WHERE
                    game_id = {$gameId}
                GROUP BY
                    member_id
            ) A
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = A.member_id
            {$havingRanking}
            ORDER BY
                ranking ASC
                ,member_id ASC
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 回答終了までの制限時間取得(ブラウザリロード対策)
     * @return int
     */
    public function calcTimeLimit()
    {
        $sql = <<< EOF
            SELECT
                TIME_TO_SEC(TIMEDIFF(DATE_ADD(QAS.renewal_time, INTERVAL Q.time_limit SECOND), NOW()))
            FROM
                quiz_admin_status QAS
            LEFT JOIN
                quiz Q
            ON
                QAS.now_quiz_id = Q.quiz_id
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * ユーザー単位のクイズ結果取得
     * @param array $adminStatus
     * @param int $memberId
     *
     * @return array
     */
    public function fetchQuizYourResult($adminStatus, $memberId)
    {
        $gameId    = $adminStatus['now_game_id'];
        $gameOrder = $adminStatus['now_game_order'];

        $sql = <<< EOF
            SELECT
                QMA.member_answer
                ,QAHO.quiz_option_text
                ,QMA.is_correct_answer
                ,QMA.is_using_super_satoshikun
                ,QMA.acquired_point
            FROM
                quiz_member_answer QMA
            LEFT JOIN
                (
                    SELECT
                        quiz_option_id
                        ,quiz_option_text
                    FROM
                        quiz_asked_history_option
                    WHERE
                        game_id = {$gameId}
                    AND game_order = {$gameOrder}
                ) QAHO
            ON
                QMA.member_answer = QAHO.quiz_option_id
            WHERE
                QMA.member_id = {$memberId}
            AND QMA.game_id = {$gameId}
            AND QMA.game_order = {$gameOrder}
        ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    ///////////////////////////////////////////
    ///           game_start               ////
    ///////////////////////////////////////////
    /**
     * ゲームIDを設定・登録
     *
     * @return int 新規ゲームID
     */
    public function registerQuizGameId($quizGameTitle)
    {
        // ゲームID 設定
        $sql = <<< EOF
            SELECT
                IFNULL(MAX(game_id), 0) + 1
            FROM
                quiz_game_history
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        $newGameId = $stmt->fetch(PDO::FETCH_COLUMN);

        // quiz_game_historyに履歴登録
        $sql = <<< EOF
            INSERT INTO quiz_game_history (
                game_id
                ,game_date
                ,game_name
                ,is_survival
            ) VALUES (
                {$newGameId}
                ,NOW(3)
                ,'{$quizGameTitle}'
                ,0
            );
EOF;
        $stmt = $this->dbh->query($sql);

        return $newGameId;
    }

    /**
     * adminstatusテーブルにゲームIDとプレイリストIDを設定
     *
     * @param int $gameId ゲームID
     * @param int $playlistId プレイリストID
     * @param int $quizStatus クイズゲームの現在の状態(quiz_status.php参照)
     * @return void
     */
    public function resetAdminStatusTable($gameId, $playlistId, $quizStatus)
    {
        $this->dbh->query('DELETE FROM quiz_admin_status');

        // スーパーさとしくん・ライフライン使用可能回数取得
        $sql = <<< EOF
            SELECT
                super_satoshikun_power_point
                ,fifty_fifty_power_point
                ,audience_power_point
            FROM
                quiz_playlist_header
            WHERE
                playlist_id = {$playlistId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        $playList = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = <<< EOF
            INSERT INTO quiz_admin_status (
                now_state
                ,renewal_time
                ,now_game_id
                ,now_playlist_id
                ,super_satoshikun_power_point
                ,fifty_fifty_power_point
                ,audience_power_point
            ) VALUES (
                {$quizStatus}
                ,NOW(3)
                ,{$gameId}
                ,{$playlistId}
                ,{$playList['super_satoshikun_power_point']}
                ,{$playList['fifty_fifty_power_point']}
                ,{$playList['audience_power_point']}
            );
EOF;
        $this->dbh->query($sql);
    }

    /**
     * クイズゲームに参加するメンバーを登録
     * @param int $gameId
     * @return void
     */
    public function resisterGameMember($gameId)
    {
        $sql = <<< EOF
            INSERT INTO quiz_game_member (
                game_id
                ,member_id
                ,member_name
            )
            SELECT
                {$gameId}
                ,member_id
                ,member_name
            FROM
                game_member GM
            WHERE
                login_state = 2
            ORDER BY
                member_id ASC
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * 参加メンバー回答用テーブルリセット(ゲーム開始時)
     *
     * @return void
     */
    public function resetMemberAnswerTable()
    {
        $this->dbh->query('DELETE FROM quiz_member_answer');
    }

    /**
     * quiz_admin_statusテーブルの情報を取得
     * @return array
     */
    public function fetchQuizAdminStatus()
    {
        $sql = <<< EOF
            SELECT
                now_state
                ,renewal_time
                ,now_game_id
                ,now_game_order
                ,now_quiz_id
                ,now_playlist_id
                ,now_playlist_order
                ,super_satoshikun_power_point
                ,fifty_fifty_power_point
                ,audience_power_point
            FROM
                quiz_admin_status
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * quiz_admin_statusのnow_quiz_idを+1して次の問題へ進む
     * @param boolean $onPlayList
     * @return void
     */
    public function goNextOrder($onPlayList)
    {
        $isUpdatePlayListOrder = '';
        if ($onPlayList) {
            $isUpdatePlayListOrder = ', now_playlist_order = IFNULL(now_playlist_order, 0) + 1';
        }

        $sql = <<< EOF
            UPDATE quiz_admin_status
            SET
                now_game_order = IFNULL(now_game_order, 0) + 1
                {$isUpdatePlayListOrder}
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * 現在使用しているプレイリストを取得
     * @return int
     */
    public function fetchNowQuizPlaylist()
    {
        $sql = <<< EOF
            SELECT
                now_playlist_id
            FROM
                quiz_admin_status
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 現在何問目かを取得
     * @return int
     */
    public function fetchNowQuizOrder()
    {
        $sql = <<< EOF
            SELECT
                IFNULL(now_game_order, 0)
            FROM
                quiz_admin_status
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 出題するクイズIDをプレイリストから取得
     * @param $playlistId プレイリストID
     * @param $quizOrder 現在何問目か
     * @return int
     */
    public function fetchQuizIdFromPlayList($playlistId, $quizOrder)
    {
        $sql = <<< EOF
            SELECT
                quiz_id
            FROM
                quiz_playlist
            WHERE
                playlist_id = {$playlistId}
            AND playlist_order = {$quizOrder}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * クイズの情報を取得(選択肢は別メソッド)
     * @param int $quizId
     * @return array
     */
    public function fetchQuiz($quizId)
    {
        $sql = <<< EOF
            SELECT
                quiz_id
                ,quiz_format
                ,is_quick_press
                ,quiz_question_sentence
                ,quiz_answer
                ,time_limit
                ,allocation
                ,fifty_fifty_chooseable
                ,audience_chooseable
            FROM
                quiz
            WHERE
                quiz_id = {$quizId}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * クイズの選択肢を取得
     * @param int $quizId
     * @return array
     */
    public function fetchQuizOption($quizId)
    {
        $sql = <<< EOF
            SELECT
                quiz_option_id
                ,quiz_option_text
                ,fifty_fifty_display
            FROM
                quiz_option
            WHERE
                quiz_id = {$quizId}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);;
    }

    /**
     * メンバーの回答を選択肢毎に取得
     * @param array $adminStatus
     *
     * @return array
     */
    public function fetchMemberAnswers($adminStatus)
    {
        $gameId    = $adminStatus['now_game_id'];
        $gameOrder = $adminStatus['now_game_order'];
        $quizId    = $adminStatus['now_quiz_id'];

        // 選択肢を取得
        $quizOption = $this->fetchQuizOption($quizId);

        foreach ($quizOption as $key => $option) {
            $quizOption[$key]['selected_member'] = [];
            $sql = <<< EOF
                SELECT
                    QMA.member_id
                    ,GM.member_name
                    ,QMA.member_answer_no
                    ,QMA.member_answer
                    ,QMA.answer_time
                    ,QMA.is_using_super_satoshikun
                FROM
                    quiz_member_answer QMA
                LEFT JOIN
                    game_member GM
                ON
                    GM.member_id = QMA.member_id
                WHERE
                    member_answer = {$option['quiz_option_id']}
                AND game_id = {$gameId}
                AND game_order = {$gameOrder}
                ;
EOF;
            $stmt = $this->dbh->query($sql);
            $selectedMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($selectedMembers)) {
                $quizOption[$key]['selected_member'] = $selectedMembers;
            }
        }
        return $quizOption;
    }

    /**
     * メンバーの回答を選択肢毎に取得(人数のみ)
     * @param array $adminStatus
     *
     * @return array
     */
    public function fetchNumberOfMemberAnswers($adminStatus)
    {
        $quizId    = $adminStatus['now_quiz_id'];
        $gameId    = $adminStatus['now_game_id'];
        $gameOrder = $adminStatus['now_game_order'];

        $numberOfMemberAnswers = [];
        $sql = <<< EOF
            SELECT
                QO.quiz_option_id
                ,COUNT(QMA.member_answer) AS number_of_members
            FROM
                quiz_option QO
            LEFT JOIN
                quiz_member_answer QMA
            ON
                QO.quiz_option_id = QMA.member_answer
            AND QMA.game_id = {$gameId}
            AND QMA.game_order = {$gameOrder}
            WHERE
                QO.quiz_id = {$quizId}
            GROUP BY
                QO.quiz_option_id
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        $numberOfMemberAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $numberOfMemberAnswers;
    }

    /**
     * クイズの正解をquizテーブルに登録
     * @param int $quizId
     * @param int $correctAnswer
     * @return void
     */
    public function updateCorrectAnswerToQuiz($quizId, $correctAnswer)
    {
        $sql = <<< EOF
            UPDATE quiz
            SET
                quiz_answer = {$correctAnswer}
            WHERE
                quiz_id = {$quizId}
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * クイズの正解をquiz_asked_historyテーブルに登録
     * @param int $gameId
     * @param int $gameOrder
     * @param int $correctAnswer
     * @return void
     */
    public function updateCorrectAnswerToQuizAskedHistory($gameId, $gameOrder, $correctAnswer)
    {
        $sql = <<< EOF
            UPDATE quiz_asked_history
            SET
                quiz_answer = {$correctAnswer}
            WHERE
                game_id = {$gameId}
            AND game_order = {$gameOrder}
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * メンバー回答の答え合わせ
     * @param array $adminStatus
     * @param string $correctAnswer
     * @return void
     */
    public function checkMemberAnswer($adminStatus, $correctAnswer)
    {
        $quizId    = $adminStatus['now_quiz_id'];
        $gameId    = $adminStatus['now_game_id'];
        $gameOrder = $adminStatus['now_game_order'];

        // 配点を取得
        $sql = <<< EOF
            SELECT
                allocation
            FROM
                quiz
            WHERE
                quiz_id = {$quizId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        $allocation = $stmt->fetch(PDO::FETCH_COLUMN);

        $isCorrectToTheQuiz = IS_CORRECT_TO_THE_QUIZ;
        $usingSuperSatoshikun = USING_SUPER_SATOSHIKUN;

        // is_correct_answer = 1 → クイズに正解
        $sql = <<< EOF
            UPDATE quiz_member_answer
            SET
                is_correct_answer = {$isCorrectToTheQuiz}
                ,acquired_point =
                                CASE
                                    WHEN is_using_super_satoshikun = {$usingSuperSatoshikun} THEN {$allocation} * 2
                                    ELSE {$allocation}
                                END
            WHERE
                game_id = '{$gameId}'
            AND game_order = '{$gameOrder}'
            AND member_answer = {$correctAnswer}
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * 取得した得点をresultテーブルにINSERTして積み上げ
     * @param array $adminStatus
     * @param int $quiz
     * @return void
     */
    public function resisterResult($adminStatus, $quiz)
    {
        $gameId    = $adminStatus['now_game_id'];
        $gameOrder = $adminStatus['now_game_order'];

        $quizId               = $quiz['quiz_id'];
        $quizQuestionSentence = $quiz['quiz_question_sentence'];
        $quizAnswer           = $quiz['quiz_answer'];
        $allocation           = $quiz['allocation'];

        $usingSuperSatoshikun = USING_SUPER_SATOSHIKUN;

        $sql = <<< EOF
            INSERT INTO quiz_result (
                game_id
                ,game_order
                ,member_id
                ,member_name
                ,quiz_id
                ,quiz_question_sentence
                ,quiz_answer
                ,allocation
                ,member_answer_no
                ,member_answer
                ,is_using_super_satoshikun
                ,is_using_fifty_fifty
                ,is_using_audience
                ,required_time
                ,is_correct_answer
                ,acquired_point
            )
            SELECT
                QMA.game_id
                ,QMA.game_order
                ,QMA.member_id
                ,GM.member_name
                ,{$quizId}
                ,'{$quizQuestionSentence}'
                ,'{$quizAnswer}'
                ,{$allocation}
                ,QMA.member_answer_no
                ,QMA.member_answer
                ,QMA.is_using_super_satoshikun
                ,QMA.is_using_fifty_fifty
                ,QMA.is_using_audience
                ,0
                ,is_correct_answer
                ,CASE
                    WHEN is_correct_answer = 0 THEN 0
                    WHEN is_using_super_satoshikun = {$usingSuperSatoshikun} THEN {$allocation} * 2
                    ELSE {$allocation}
                END AS acquired_point
            FROM
                quiz_member_answer QMA
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = QMA.member_id
            WHERE
                QMA.game_id = {$gameId}
            AND QMA.game_order = {$gameOrder}
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * クイズの正解を取得
     * @param int $gameId
     * @param int $gameOrder
     * @return array
     */
    public function fetchCorrectAnswer($gameId, $gameOrder)
    {
        $sql = <<< EOF
            SELECT
                QAS.quiz_answer
                ,QAHO.quiz_option_text
            FROM
                quiz_asked_history QAS
            LEFT JOIN
                (
                    SELECT
                        quiz_option_id
                        ,quiz_option_text
                    FROM
                        quiz_asked_history_option
                    WHERE
                        game_id = {$gameId}
                    AND game_order = {$gameOrder}
                ) QAHO
            ON
                QAS.quiz_answer = QAHO.quiz_option_id
            WHERE
                QAS.game_id = {$gameId}
            AND QAS.game_order = {$gameOrder}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * クイズ正解者一覧を取得
     *
     * @param int $gameId
     * @param int $gameOrder
     *
     * @return array
     */
    public function fetchCorrectAnswerMembers($gameId, $gameOrder)
    {
        // 正解者 → is_correct_answer = 1
        $isCorrectToTheQuiz = IS_CORRECT_TO_THE_QUIZ;

        $sql = <<< EOF
            SELECT
                QMS.member_id
                ,GM.member_name
                ,QMS.answer_time
                ,QMS.is_using_super_satoshikun
            FROM
                quiz_member_answer QMS
            LEFT JOIN
                game_member GM
            ON
                GM.member_id = QMS.member_id
            WHERE
                QMS.game_id = {$gameId}
            AND QMS.game_order = {$gameOrder}
            AND QMS.is_correct_answer = {$isCorrectToTheQuiz}
EOF;
            $stmt = $this->dbh->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 次に出題予定のクイズを取得
     * @param int $playlistId
     * @param int $playlistOrder
     *
     * @return array
     */
    public function fetchNextQuiz($playListId, $playListOrder)
    {
        $nextPlayListOrder = $playListOrder + 1;
        // プレイリストから次の出題予定のクイズIDを取得
        $sql = <<< EOF
            SELECT
                quiz_id
            FROM quiz_playlist
            WHERE
                playlist_id = {$playListId}
            AND playlist_order = {$nextPlayListOrder}
EOF;
        $stmt = $this->dbh->query($sql);
        $nextQuizId = $stmt->fetch(PDO::FETCH_COLUMN);

        if (empty($nextQuizId)) {
            return [];
        }

        return $this->fetchQuiz($nextQuizId);
    }


// FIXME:クイズ進行のメソッドはこの上に足していきましょう！


    /**
     * 作成したクイズ一覧を取得
     *
     * @return array
     */
    public function getCreatedQuizzes()
    {
        $sql = <<< EOF
            SELECT
                quiz_id
                ,quiz_format
                ,is_quick_press
                ,quiz_question_sentence
                ,quiz_answer
                ,time_limit
                ,allocation
            FROM
                quiz
            ORDER BY
                quiz_id ASC
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 選択肢の配列を$quizzes[key]['option']に追加
        foreach ($quizzes as $key => $quiz) {
            $sql = <<< EOF
                SELECT
                    quiz_option_id
                    ,quiz_option_text
                FROM
                    quiz_option
                WHERE
                    quiz_id = {$quiz['quiz_id']}
                ORDER BY
                    quiz_option_id ASC
            ;
EOF;

            $stmt = $this->dbh->query($sql);
            $quizOption = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $quizzes[$key]['quiz_option'] = $quizOption;
        }

        return $quizzes;
    }

    /**
     * 問題文省略表記
     * @param string $sentence 問題文
     * @param int $maxLength 何文字まで表示するか
     *
     * @return string
     */
    public function abbreviateSentence($sentence, $maxLength = 10)
    {
        $sentenceLength = mb_strlen($sentence);
        if ($sentenceLength <= $maxLength) {
            return $sentence;
        }

        $formatSentence = mb_substr($sentence, 0, $maxLength). '…';
        return $formatSentence;
    }

    /**
     * 出題履歴テーブルへのINSERT
     * @param array $adminStatusArray ゲームID, 出題番号
     * @param array $quiz クイズ情報
     * @return void
     */
    public function resisterQuizAskedHistory($adminStatusArray, $quiz)
    {
        $gameId = $adminStatusArray['now_game_id'];
        $gameOrder = $adminStatusArray['now_game_order'];
        $quizQuestionSentence = $quiz['quiz_question_sentence'];
        $allocation = $quiz['allocation'];

        $sql = <<< EOF
            INSERT INTO quiz_asked_history (
                game_id
                ,game_order
                ,quiz_start_time
                ,quiz_question_sentence
                ,allocation
            ) VALUES (
                {$gameId}
                ,{$gameOrder}
                ,NOW(3)
                ,'{$quizQuestionSentence}'
                ,{$allocation}
            );
EOF;
        $this->dbh->query($sql);

        $quizId = $quiz['quiz_id'];
        $sql = <<< EOF
            INSERT INTO quiz_asked_history_option (
                game_id
                ,game_order
                ,quiz_option_id
                ,quiz_option_text
            )
            SELECT
                {$gameId}
                ,{$gameOrder}
                ,quiz_option_id
                ,quiz_option_text
            FROM
                quiz_option
            WHERE
                quiz_id = {$quizId}
            ORDER BY
                quiz_option_id ASC
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * admin_statusテーブルのクイズID更新
     * @param int $newQuizId
     * @return void
     */
    public function updateAdminQuizId($newQuizId)
    {
        $sql = <<< EOF
            UPDATE quiz_admin_status
            SET
                renewal_time = NOW(3)
                ,now_quiz_id = {$newQuizId}
EOF;
        $this->dbh->query($sql);
    }

    /**
     * メンバー回答テーブルに回答用レコードを準備
     * @param int $gameId
     * @param int $gameOrder
     * @return void
     */
    public function setMemberAnswerTable($gameId, $gameOrder)
    {
        $sql = <<< EOF
            INSERT INTO quiz_member_answer(
                member_id
                ,game_id
                ,game_order
                ,member_answer_no
                ,member_answer
                ,answer_time
            )
            SELECT
                member_id
                ,{$gameId}
                ,{$gameOrder}
                ,1
                ,'0'
                ,NULL
            FROM
                quiz_game_member
            WHERE
                game_id = {$gameId}
            ;
EOF;
        $this->dbh->query($sql);
}

    /**
     * admin_statusのステータス変更(この作業でユーザー画面が遷移する)
     * @param int $newStatus 更新するステータス
     * @return void
     */
    public function updateQuizStatus($newStatus)
    {
        $sql = <<< EOF
            UPDATE quiz_admin_status
            SET
                now_state = {$newStatus}
                ,renewal_time = NOW(3)
            ;
EOF;
        $this->dbh->query($sql);
    }

    /**
     * 作成したクイズプレイリスト一覧を取得
     *
     * @return array
     */
    public function getCreatedQuizPlaylists()
    {
        $sql = <<< EOF
            SELECT
                playlist_id
                ,playlist_title
                ,super_satoshikun_power_point
            FROM
                quiz_playlist_header
            ORDER BY
                playlist_id ASC
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        $quizPlaylists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 選択肢の配列を$quizPlaylists[key]['playlist']に追加
        foreach ($quizPlaylists as $key => $list) {
            $sql = <<< EOF
                SELECT
                    QP.playlist_order
                    ,QP.quiz_id
                    ,Q.quiz_question_sentence
                FROM
                    quiz_playlist QP
                LEFT JOIN
                    quiz Q
                ON
                    Q.quiz_id = QP.quiz_id
                WHERE
                    playlist_id = {$list['playlist_id']}
                ORDER BY
                    playlist_order ASC
            ;
EOF;

            $stmt = $this->dbh->query($sql);
            $playlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $quizPlaylists[$key]['playlist'] = $playlist;
        }

        return $quizPlaylists;
    }

    /////////////////////////////////////////////
    ///          create_quiz_done             ///
    /////////////////////////////////////////////
    /**
     * 新しくクイズを作成する
     *
     * @param array $post 登録内容
     *
     * return void
     */
    public function createNewQuiz($post)
    {
        // TODO:トランザクション処理

        // 登録するIDを取得
        $sql = 'SELECT IFNULL(MAX(quiz_id), 0) + 1 FROM quiz';
        $stmt = $this->dbh->query($sql);
        $quizId = $stmt->fetch(PDO::FETCH_COLUMN);

        // クイズテーブルへ登録
        $sql = <<< EOF
            INSERT INTO quiz(
                quiz_id
                ,quiz_format
                ,is_quick_press
                ,quiz_question_sentence
                ,quiz_answer
                ,time_limit
                ,allocation
                ,fifty_fifty_chooseable
            ) VALUES (
                {$quizId}
                ,1
                ,0
                ,'{$post["quiz_question_sentence"]}'
                ,'{$post["quiz_answer"]}'
                ,{$post["time_limit"]}
                ,{$post["allocation"]}
                ,'{$post["fifty_fifty_chooseable"]}'
            );
EOF;
        $stmt = $this->dbh->query($sql);

        // 選択肢をクイズオプションテーブルに登録
        $i = 0;
        while (!empty($post['quiz_option_id_'. $i])) {
            $sql = <<< EOF
                INSERT INTO quiz_option(
                    quiz_id
                    ,quiz_option_id
                    ,quiz_option_text
                    ,fifty_fifty_display
                ) VALUES (
                    {$quizId}
                    ,'{$post["quiz_option_id_". $i]}'
                    ,'{$post["quiz_option_text_". $i]}'
                    ,'{$post["fifty_fifty_display_". $i]}'
                );
EOF;
            $stmt = $this->dbh->query($sql);

            $i = $i + 1;
        }
    }

    /**
     * クイズを変更する
     *
     * @param array $post 登録内容
     * @param int $quizId クイズID(quizテーブルの主キー)
     *
     * return void
     */
    public function updateQuiz($post, $quizId)
    {
        // TODO:トランザクション処理
        // クイズテーブルの更新
        $sql = <<< EOF
            UPDATE quiz
            SET
                quiz_format = 1
                ,is_quick_press = 0
                ,quiz_question_sentence = '{$post["quiz_question_sentence"]}'
                ,quiz_answer = '{$post["quiz_answer"]}'
                ,time_limit = {$post["time_limit"]}
                ,allocation = {$post["allocation"]}
                ,fifty_fifty_chooseable = '{$post["fifty_fifty_chooseable"]}'
            WHERE
                quiz_id = {$quizId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);

        // クイズ選択肢テーブルの更新 (DELETE → INSERT)
        $sql = <<< EOF
            DELETE FROM quiz_option
            WHERE
                quiz_id = {$quizId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);

        $i = 0;
        while (!empty($post['quiz_option_id_'. $i])) {
            $sql = <<< EOF
                INSERT INTO quiz_option(
                    quiz_id
                    ,quiz_option_id
                    ,quiz_option_text
                    ,fifty_fifty_display
                ) VALUES (
                    {$quizId}
                    ,'{$post["quiz_option_id_". $i]}'
                    ,'{$post["quiz_option_text_". $i]}'
                    ,'{$post["fifty_fifty_display_". $i]}'
                );
EOF;
            $stmt = $this->dbh->query($sql);

            $i = $i + 1;
        }
    }

    /**
     * クイズを削除する
     *
     * @param int $quizId クイズID(quizテーブルの主キー)
     *
     * return void
     */
    public function deleteQuiz($quizId)
    {
        // TODO:トランザクション処理
        // クイズテーブルの更新
        $sql = <<< EOF
            SELECT count(playlist_id)
            FROM
                quiz_playlist
            WHERE
                quiz_id = {$quizId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
        $quizCnt = $stmt->fetch(PDO::FETCH_COLUMN);

        if ($quizCnt > 0) {
            echo 'プレイリストに登録されているため削除できません';
            return;
        }

        // クイズ選択肢テーブルの更新 (DELETE → INSERT)
        $sql = <<< EOF
            DELETE FROM quiz_option
            WHERE
                quiz_id = {$quizId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);

        // クイズの削除
        $sql = <<< EOF
        DELETE FROM quiz
        WHERE
            quiz_id = {$quizId}
        ;
EOF;

        $stmt = $this->dbh->query($sql);
    }

    ///////////////////////////////////////////
    ///     create_quiz_playlist_edit       ///
    ///////////////////////////////////////////
    /**
     * クイズプレイリストヘッダーの情報を取得
     * @param int $playlistId
     * @return array
     */
    public function fetchQuizPlaylist($playlistId)
    {
        $sql = <<< EOF
            SELECT
                playlist_id
                ,playlist_title
                ,super_satoshikun_power_point
                ,fifty_fifty_power_point
                ,audience_power_point
            FROM
                quiz_playlist_header
            WHERE
                playlist_id = {$playlistId}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * クイズプレイリストの詳細を取得
     * @param int $playlistId
     * @return array
     */
    public function fetchQuizPlaylistOrder($playlistId)
    {
        $sql = <<< EOF
            SELECT
                playlist_order
                ,quiz_id
            FROM
                quiz_playlist
            WHERE
                playlist_id = {$playlistId}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);;
    }

    /////////////////////////////////////////////
    ///     create_quiz_playlist_conf         ///
    /////////////////////////////////////////////
    /**
     * クイズの問題文を取得
     * @param int $quizId
     * @return string
     */
    public function fetchQuizSentence($quizId)
    {
        $sql = <<< EOF
            SELECT
                quiz_question_sentence
            FROM
                quiz
            WHERE
                quiz_id = {$quizId}
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /////////////////////////////////////////////
    ///     create_quiz_playlist_done         ///
    /////////////////////////////////////////////
    /**
     * クイズの問題文を取得
     * @param array $post 登録内容
     * @return void
     */
    public function createNewQuizPlaylist($post)
    {
        // TODO:トランザクション処理

        // 登録するIDを取得
        $sql = 'SELECT IFNULL(MAX(playlist_id), 0) + 1 FROM quiz_playlist_header';
        $stmt = $this->dbh->query($sql);
        $playlistId = $stmt->fetch(PDO::FETCH_COLUMN);

        // プレイリストヘッダーテーブルへ登録
        $sql = <<< EOF
            INSERT INTO quiz_playlist_header(
                playlist_id
                ,playlist_title
                ,super_satoshikun_power_point
            ) VALUES (
                {$playlistId}
                ,'{$post["playlist_title"]}'
                ,{$post["super_satoshikun_power_point"]}
            );
EOF;
        $stmt = $this->dbh->query($sql);

        // プレイリストテーブルに登録
        $i = 0;
        while (!empty($post['playlist_order_'. $i])) {
            $sql = <<< EOF
                INSERT INTO quiz_playlist(
                    playlist_id
                    ,playlist_order
                    ,quiz_id
                ) VALUES (
                    {$playlistId}
                    ,{$post["playlist_order_". $i]}
                    ,{$post["quiz_id_". $i]}
                );
EOF;
            $stmt = $this->dbh->query($sql);

            $i = $i + 1;
        }
    }

    /**
     * プレイリストを修正する
     *
     * @param array $post 登録内容
     * @param int $playlistId プレイリストID(quiz_playlistテーブルの主キー)
     *
     * return void
     */
    public function updateQuizPlayList($post, $playlistId)
    {
        // TODO:トランザクション処理
        // クイズテーブルの更新
        $sql = <<< EOF
            UPDATE quiz_playlist_header
            SET
                playlist_title = '{$post["playlist_title"]}'
                ,super_satoshikun_power_point = {$post["super_satoshikun_power_point"]}
                ,fifty_fifty_power_point = {$post["fifty_fifty_power_point"]}
                ,audience_power_point = {$post["audience_power_point"]}
            WHERE
                playlist_id = {$playlistId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);

        // クイズ選択肢テーブルの更新 (DELETE → INSERT)
        $sql = <<< EOF
            DELETE FROM quiz_playlist
            WHERE
                playlist_id = {$playlistId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);

        $i = 0;
        while (!empty($post['playlist_order_'. $i])) {
            $sql = <<< EOF
                INSERT INTO quiz_playlist(
                    playlist_id
                    ,playlist_order
                    ,quiz_id
                ) VALUES (
                    {$playlistId}
                    ,{$post["playlist_order_". $i]}
                    ,{$post["quiz_id_". $i]}
                );
EOF;
            $stmt = $this->dbh->query($sql);

            $i = $i + 1;
        }
    }

    /**
     * プレイリストを修正する
     *
     * @param int $playlistId プレイリストID(quiz_playlistテーブルの主キー)
     *
     * return void
     */
    public function deletePlayList($playlistId)
    {
        // TODO:トランザクション処理
        // クイズテーブルの更新
        $sql = <<< EOF
            DELETE FROM quiz_playlist_header
            WHERE
                playlist_id = {$playlistId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);

        // クイズ選択肢テーブルの更新 (DELETE → INSERT)
        $sql = <<< EOF
            DELETE FROM quiz_playlist
            WHERE
                playlist_id = {$playlistId}
            ;
EOF;
        $stmt = $this->dbh->query($sql);
    }

    /**
     * クイズゲーム開催履歴を取得
     *
     * @return array
     */
    public function fetchQuizGameHistory()
    {
        $sql = <<< EOF
            SELECT
                game_id
                ,game_name
                ,game_date
            FROM
                quiz_game_history
            ORDER BY
                game_id ASC
            ;
EOF;

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
