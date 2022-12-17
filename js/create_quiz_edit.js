var app = new Vue({
    el: '#app',
    data: {
        optionTag : JSON.parse(quizOptionTagJson),
    },
    methods : {
        addOptionTag : function() {
            let numOfOptionTag = Object.keys(this.optionTag).length;
            let addData = {
                quiz_option_id : numOfOptionTag + 1,
                quiz_option_text : '',
                fifty_fifty_display : '1',
            };
            this.optionTag.push(addData);
        },
        deleteOptionTag : function(deleteId) {
            // 選択肢が1つの場合は消去不可 optionTagにobserverが含まれるため、実質「length1→要素0」
            if (this.optionTag.length > 1) {
                this.optionTag.splice(deleteId, 1);
            }
        }
    }
});

const timeLimitArea = document.getElementById('time_limit');
timeLimitArea.addEventListener('change', (e) => {
    const changedValue = e.target.value;
    if (isNaN(changedValue)) {
        alert('制限時間は数値を入力して下さい');
        e.target.value = timeLimit;
    }

    if (changedValue < 10 || changedValue > 120) {
        alert('制限時間は10秒以上、120秒以下に設定してください');
        e.target.value = timeLimit;
    }
});
