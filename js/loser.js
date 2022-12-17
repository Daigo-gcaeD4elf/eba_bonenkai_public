let openModalBtn  = document.getElementById('open_modal');
let closeModalBtn = document.getElementById('close_modal');

let modal = document.getElementById('modal');

let rps = document.getElementsByClassName('rps');

let refinfo = document.referrer;
if (!refinfo) {
    alert('直リンクはご遠慮下さいませ(;・∀・)');
}

// openModal('ゲームスタート');

// openModalBtn.addEventListener('click', () => {
//     openModal('閉じる');
// });

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
    data: {
        hoge: '表示できるかな？'
    }
});

let chkCountdown = setInterval(submitPage, 1000);

function submitPage() {
    let timer = document.getElementById('countdown');
    let remainingTime = timer.innerHTML;
    if (isNaN(remainingTime)) {
        setTimeout(submitPreResultPage, 2000);
    }
}

function submitPreResultPage() {
    document.toResult.submit();
}
