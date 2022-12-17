let refinfo = document.referrer;
if (!refinfo) {
    alert('直リンクはご遠慮下さいませ(;・∀・)');
}

Vue.component('countdown', {
    data: function() {
        return {
            timeLimit: timeLimit,
            message: '',
            nowDateTime: '',
            startDateTime: '',
            finishDateTime: '',
        }
    },
    mounted: function() {
        this.nowDateTime = new Date();
        this.startDateTime = this.nowDateTime;
        this.finishDateTime = this.nowDateTime;
        this.finishDateTime.setSeconds(this.finishDateTime.getSeconds() + Number(this.timeLimit));

        this.countDown();
        setInterval(this.countDown, 1000);
    },
    methods: {
        countDown: function() {
            let diffDateTime = 0;
            if (this.timeLimit > 0) {
                this.nowDateTime = new Date();

                diffDateTime = Math.floor((this.finishDateTime.getTime() - this.nowDateTime.getTime()) / 1000);
                if (diffDateTime < 0) {
                    diffDateTime = 0;
                }
                this.timeLimit = diffDateTime;

                this.message = `残り ${this.timeLimit} 秒`;
            } else {
                this.timeLimit = 0;
                this.message = '少々お待ちください・・・';

                // クイズ選択肢のラジオボタンを非活性にする
                let radioButton = document.getElementsByClassName('js_your_answer');
                let radioButtonLength = radioButton.length;

                for (let i = 0; i < radioButtonLength; i++) {
                    radioButton[i].setAttribute('disabled', true);
                }

                // スーパーさとしくんのラジオボタン(2つ)を非活性にする
                let superSatoshikunButton = document.getElementsByClassName('js_super_satoshikun');
                let suberSatoshikunButtonLength = superSatoshikunButton.length;

                for (let i = 0; i < suberSatoshikunButtonLength; i++) {
                    superSatoshikunButton[i].setAttribute('disabled', true);
                }

                setTimeout(submitPage, 3000);
            }
        }
    },
    template: '<div v-text="message" class="text-center font-bold text-lg" id="js_countdown"></div>'
});

var app = new Vue({
    el: '#app_countdown',
});

function submitPage() {
    document.quizTimeup.submit();
}

let answerData = {
    'fnc_name' : 'changeMemberAnswer',
    'member_id' : memberId,
    'game_id' : gameId,
    'game_order' : gameOrder,
    'your_answer' : '',
};

/* 回答ボタン選択関連 */
// クリックイベントはHTMLに記載
function resisterYourAnswer(val) {
    answerData.your_answer = val;
    updateAnswerDataOnAjax(answerData);
}

function updateAnswerDataOnAjax(data) {
    let xhr = new XMLHttpRequest();

    xhr.open('POST', './Ajax.php');
    xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhr.send(encodeHtmlForm(data));
}

/* スーパーさとしくん使用関連 */
// クリックイベントはHTMLに記載
let supersatoshikunData = {
    'fnc_name' : 'changeIsUsingSupersatoshikun',
    'member_id' : memberId,
    'game_id' : gameId,
    'game_order' : gameOrder,
    'is_using_super_satoshikun' : '',
}

function changeIsUsingSupersatoshikun(val) {
    if (superSatoshikunStock <= 0) {
        return;
    }

    supersatoshikunData.is_using_super_satoshikun = val;
    updateAnswerDataOnAjax(supersatoshikunData);
}

/* 50-50 ボタン押下 */
let fiftyfiftyBtn  = document.getElementById('js_use_fifty_fifty_button');
fiftyfiftyBtn.addEventListener('click', () => {

    if (fiftyFiftyStock <= 0) {
        return;
    }

    if (fiftyFiftyChooseAble === '0') {
        return;
    }

    // ボタンにボーダーラインを付与
    // fiftyfiftyBtn.classList.remove('border-transparent');
    // fiftyfiftyBtn.classList.add('border-indigo-600');
    fiftyfiftyBtn.innerHTML = '<img src="../img/active_fifty_fifty.png?now=<?=date(\'YmdHis\')?>" alt="">';

    setTimeout(() => {
        if (confirm('50-50を使用します。\n よろしいですか？')) {
            document.useFiftyFifty.submit();
        }

        // ボタンのボーダーラインを削除
        // fiftyfiftyBtn.classList.remove('border-indigo-600');
        // fiftyfiftyBtn.classList.add('border-transparent');
        fiftyfiftyBtn.innerHTML = '<img src="../img/available_fifty_fifty.png?now=<?=date(\'YmdHis\')?>" alt="">';
    }, 100);
});

/* オーディエンス ボタン押下 */
let audienceBtn  = document.getElementById('js_use_audience_button');
audienceBtn.addEventListener('click', () => {

    if (audienceStock <= 0) {
        return;
    }

    if (audienceChooseAble === '0') {
        return;
    }

    // ボタンにボーダーラインを付与
    // audienceBtn.classList.remove('border-transparent');
    // audienceBtn.classList.add('border-indigo-600');
    audienceBtn.innerHTML = '<img src="../img/active_audience.png?now=<?=date(\'YmdHis\')?>" alt="">';

    setTimeout(() => {
        if (confirm('オーディエンスを使用します。\n よろしいですか？')) {
            document.useAudience.submit();
        }

        // ボタンのボーダーラインを削除
        // audienceBtn.classList.remove('border-indigo-600');
        // audienceBtn.classList.add('border-transparent');
        audienceBtn.innerHTML = '<img src="../img/available_audience.png?now=<?=date(\'YmdHis\')?>" alt="">';

    }, 100);
});

let adminStatus = {
    'fnc_name' : 'fetchNumberOfMemberAnswers',
    'quiz_id' : quizId,
    'game_id' : gameId,
    'game_order' : gameOrder,
}

/* オーディエンス使用時、全員の回答状況を表示 */
if (isUsingAudience === '1') {
    setAudienceHtml(JSON.parse(memberAnswersJson));

    setInterval(function() {
        fetchMembersAnswerOnAjax(adminStatus);
    }, 1000);
}

function fetchMembersAnswerOnAjax(data) {
    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            setAudienceHtml(JSON.parse(xhr.responseText));
        }
    }

    xhr.open('POST', './Ajax.php');
    xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhr.send(encodeHtmlForm(data));
}

// オーディエンス選択時の結果表示
function setAudienceHtml(memberAnswersArray) {
    memberAnswersArray.forEach(element => {
        let audienceSpanTag = document.getElementById(`js_members_answer_${element.quiz_option_id}`);
        audienceSpanTag.innerHTML = element.number_of_members;
    });
}