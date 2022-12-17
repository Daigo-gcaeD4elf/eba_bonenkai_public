// let modal = document.getElementById('modal');

// let rps = document.getElementsByClassName('rps');

let adminerRps = document.getElementById('adminer_rps');

// モーダルウィンドウの表示
// function openModal(btnMsg) {
//     modal.style.display = 'block';
//     baz.innerHTML = btnMsg;
// }

// baz.addEventListener('click', () => {
//     closeModal();
// });

// // モーダルを閉じる
// function closeModal() {
//     modal.style.display = 'none';
// }


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
                this.time = 0;
            }
        }
    },
    template: '<div v-text="time" id="countdown"></div>'
});

var app = new Vue({
    el: '#bar',
    data: {
        hoge: '表示できるかな？'
    }
});

let chkCountdown = setInterval(submitPage, 1000);

let submitFlg = 0;
let submitTimer = 3;
function submitPage() {
    let timer = document.getElementById('countdown');
    let remainingTime = timer.innerHTML;

    if (remainingTime === '0') {
        setTimeout(submitResultPage, 1000);
    }
    // if (remainingTime === '0' && submitFlg === 0) {
    //     submitFlg = 1;
    //     setTimeout(submitResultPage, 8000);
    // }
}

function submitResultPage() {
    document.toResult.submit();
}

let rock     = document.getElementById('rock');
let scissors = document.getElementById('scissors');
let paper    = document.getElementById('paper');

let data = {
    'fnc_name' : '',
    'rps' : 1,
};

rock.addEventListener('click', () => {
    changeAdminerRps(1);
});

scissors.addEventListener('click', () => {
    changeAdminerRps(2);
});

paper.addEventListener('click', () => {
    changeAdminerRps(3);
});

function changeAdminerRps(val) {
    data.fnc_name = 'changeAdminerRps';
    data.rps = val;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = () => {
        if (xhr.readyState == 4) { // 通信の完了時
            if (xhr.status == 200) { // 通信の成功時
                adminerRps.innerHTML = xhr.responseText + 'を選択しています';
            }
        } else {
            adminerRps.innerHTML = '通信中...';
        }
    }

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