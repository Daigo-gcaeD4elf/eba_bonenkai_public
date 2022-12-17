let bakResTxt = '';
function chkQuizGameState(data) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = () => {
        if (xhr.readyState == 4 && xhr.status == 200) { // 通信の完了時 かつ 通信成功時
            if (xhr.responseText !== bakResTxt && bakResTxt !== '') {
                submitPage(xhr.responseText); // 各ページのJSにsubmitPageメソッドを定義しておくこと。。
            } else {
                bakResTxt = xhr.responseText;
            }
        }
    }

    xhr.open('POST', './Ajax.php');
    xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhr.send(encodeHtmlForm(data));
}

let watchGameStateData = {
    'fnc_name' : 'chkQuizGameState',
};

let watchAdminOperation = setInterval(function(){chkQuizGameState(watchGameStateData)}, 1000);