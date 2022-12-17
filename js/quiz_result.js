let refinfo = document.referrer;
if (!refinfo) {
    alert('直リンクはご遠慮下さいませ(;・∀・)');
}

function submitPage(res) {
    document.goToNextPage.submit();
}

// 回答者の表示非表示切り替え
function toggleShowTable() {
    let btnId = document.getElementById('js_toggle_btn');
    let btnMsg = btnId.innerText;
    let tableId = document.getElementById('js_toggle_table');

    if (btnMsg === '表示') {
        btnId.innerText = '非表示';
        tableId.style.display = 'table';
    }

    if (btnMsg === '非表示') {
        btnId.innerText = '表示';
        tableId.style.display = 'none';
    }
}