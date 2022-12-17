<?php
const STAND_BY_STATUS       = 0; // スタンバイ (ゲーム始まっていない)
const ASK_QUESTION_STATUS   = 1; // 回答選択時間
const WAITING_ANSWER_STATUS = 2; // 答え合わせ待機中
const RESULT_STATUS         = 3; // 正解発表
const FINISH_GAME_STATUS    = 4; // ゲーム終了

const NOT_USING_SUPER_SATOSHIKUN = 0; // スーパーさとしくん使用していない
const USING_SUPER_SATOSHIKUN     = 1; // スーパーさとしくん使用している

const NOT_USING_LIFE_LINE = 0; // ライフラインを使用していない
const USING_LIFE_LINE     = 1; // ライフラインを使用している

const NOT_CORRECT_TO_THE_QUIZ = 0; // クイズに不正解 or 採点前
const IS_CORRECT_TO_THE_QUIZ  = 1; // クイズに正解

const MAX_FETCH_RANKING      = 10;  // DBから取得する順位のMAX
const MAX_VIEW_RANKING_LIST  = 15;  // これ以上該当者がいたら表示しない (10位までに入るメンバーが100人を越える際の対策)