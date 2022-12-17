// チェックなしでボタンクリックした場合にalertを表示
function submitCheck() {
    let correctAnswerClass = document.getElementsByClassName('js_correct_answer');
    let checkFlg = false;

    for (let i = 0; i < correctAnswerClass.length; i++) {
        if (correctAnswerClass[i].checked) {
            checkFlg = true;
            break;
        }
    }

    if (!checkFlg) {
        alert('正解を設定してください');
        return;
    }

    document.goToCheckMembersAnswer.submit();
}

// 回答者の表示非表示切り替え
function toggleShowTable(id, value) {
    let btnId = document.getElementById(id);
    let btnMsg = btnId.innerText;
    let tableId = document.getElementById(`js_toggle_table_${value}`);

    if (btnMsg === '表示') {
        btnId.innerText = '非表示';
        tableId.style.display = 'table';
    }

    if (btnMsg === '非表示') {
        btnId.innerText = '表示';
        tableId.style.display = 'none';
    }
}