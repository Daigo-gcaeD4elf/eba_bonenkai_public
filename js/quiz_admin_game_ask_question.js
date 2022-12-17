Vue.component('countdown', {
    data: function() {
        return {
            time: timeLimit,
            message: ''
        }
    },
    mounted: function() {
        this.countDown();
        setInterval(this.countDown, 1000);
    },
    methods: {
        countDown: function() {
            if (this.time > 0) {
                this.time--;
                this.message = `残り ${this.time} 秒`;
            } else {
                this.time = 0;
                this.message = '少々お待ちください・・・';
                submitPage();
            }
        }
    },
    template: '<div v-text="message" class="text-center font-bold text-lg" id="js_countdown"></div>'
});

var app = new Vue({
    el: '#app_countdown',
});

function submitPage() {
    document.goToDecideAnswer.submit();
}