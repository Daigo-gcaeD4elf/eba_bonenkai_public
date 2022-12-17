let btn = document.getElementById('btn');
let openModalBtn  = document.getElementById('open_modal');
let closeModalBtn = document.getElementById('close_modal');

let modal = document.getElementById('modal');

let rps = document.getElementsByClassName('rps');
let rpsLength = rps.length;

let refinfo = document.referrer;
if (!refinfo) {
    alert('直リンクはご遠慮下さいませ(;・∀・)');
}

// openModal('ゲームスタート');

openModalBtn.addEventListener('click', () => {
    openModal('閉じる');
});

// モーダルウィンドウの表示
function openModal(btnMsg) {
    modal.style.display = 'block';
    closeModalBtn.innerHTML = btnMsg;
}

closeModalBtn.addEventListener('click', () => {
    closeModal();
});

// モーダルを閉じる
function closeModal() {
    modal.style.display = 'none';
}


Vue.component('countdown', {
    data: function() {
        return {
            time: timeLimit
        }
    },
    mounted: function() {
        var fncTime = setInterval(this.countDown, 1000);
    },
    methods: {
        countDown: function() {
            if (this.time > 0) {
                this.time--;
            } else {
                this.time = '少々お待ちください・・・';
            }
        }
    },
    template: '<div v-text="time" id="countdown"></div>'
});

var app = new Vue({
    el: '#bar',
});

let chkCountdown = setInterval(submitPage, 1000);

function submitPage() {
    let timer = document.getElementById('countdown');
    let remainingTime = timer.innerHTML;

    if (isNaN(remainingTime) || remainingTime === '0') {
        for (let i = 0; i < rpsLength; i++) {
            rps[i].setAttribute('disabled', true);
        }
        setTimeout(submitPreResultPage, 3000);
    }
}

function submitPreResultPage() {
    document.toResult.submit();
}

let rock = document.getElementById('rock');
let scissors = document.getElementById('scissors');
let paper = document.getElementById('paper');

let data = {
    'fnc_name' : 'changeRps',
    'member_id' : id, // index.htmlから
    'rps' : 1,
};

rock.addEventListener('click', () => {
    changeRps(1);
});

scissors.addEventListener('click', () => {
    changeRps(2);
});

paper.addEventListener('click', () => {
    changeRps(3);
});

function changeRps(val) {
    data.rps = val;

    let xhr = new XMLHttpRequest();
    // xhr.onreadystatechange = () => {
    //     if (xhr.readyState == 4) { // 通信の完了時
    //         if (xhr.status == 200) { // 通信の成功時
    //             foo.innerHTML = xhr.responseText + 'を選択しています';
    //         }
    //     } else {
    //         foo.innerHTML = '通信中...';
    //     }
    // }

    xhr.open('POST', './Ajax.php');
    xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhr.send(encodeHtmlForm(data));
}

function encodeHtmlForm(data)
{
    let params = [];

    for (let name in data) {
        let value = data[name];
        let param = encodeURIComponent(name) + '=' + encodeURIComponent(value);

        params.push(param);
    }

    return params.join( '&' ).replace( /%20/g, '+' );
}