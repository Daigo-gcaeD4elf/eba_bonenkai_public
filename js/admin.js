let startBtn  = document.getElementById('startBtn');

startBtn.addEventListener('click', () => {
    if (confirm('じゃんけんゲームを始めます。\n よろしいですか？')) {
        document.startGame.submit();
    }
});
