// let modal = document.getElementById('modal');

let nextBtn   = document.getElementById('next');
let finishBtn = document.getElementById('finish');
let backBtn   = document.getElementById('back');

nextBtn.addEventListener('click', () => {
    if (confirm('次のじゃんけんを始めます。\n よろしいですか？')) {
        document.nextGame.submit();
    }
});

finishBtn.addEventListener('click', () => {
    if (confirm('ゲームをリセットします。\n よろしいですか？')) {
        document.finishGame.submit();
    }
});

backBtn.addEventListener('click', () => {
    if (confirm('じゃんけんを1つ戻します。\n よろしいですか？')) {
        console.log('true');
    } else {
        console.log('false');
    }
});

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


// Vue.component('countdown', {
//     data: function() {
//         return {
//             time: timeLimit
//         }
//     },
//     mounted: function() {
//         var fncTime = setInterval(this.countDown, 1000);
//     },
//     methods: {
//         countDown: function() {
//             if (this.time > 0) {
//                 this.time--;
//             }
//         }
//     },
//     template: '<div v-text="time" id="countdown"></div>'
// });

// var app = new Vue({
//     el: '#bar',
//     data: {
//         hoge: '表示できるかな？'
//     }
// });
