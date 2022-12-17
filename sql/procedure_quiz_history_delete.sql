-- CALL procedure_quiz_history_delete(game_id);

DELIMITER |

CREATE PROCEDURE procedure_quiz_history_delete
(
    IN gameId INT
)
BEGIN
    DELETE FROM quiz_game_history WHERE game_id = gameId;
    DELETE FROM quiz_game_member WHERE game_id = gameId;
    DELETE FROM quiz_asked_history WHERE game_id = gameId;
    DELETE FROM quiz_asked_history_option WHERE game_id = gameId;
    DELETE FROM quiz_result WHERE game_id = gameId;
END

|

DELIMITER ;