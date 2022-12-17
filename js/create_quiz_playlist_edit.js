var app = new Vue({
    el: '#app',
    data: {
        playlistOrder : JSON.parse(playlistOrderJson),
        createdQuizzes : JSON.parse(createdQuizzesJson),
    },
    methods : {
        addPlaylistOrder : function() {
            let addData = {
                quiz_id : '',
            };
            this.playlistOrder.push(addData);
        },
        deletePlaylistOrder : function(deleteId) {
            this.playlistOrder.splice(deleteId, 1);
        },
    }
});