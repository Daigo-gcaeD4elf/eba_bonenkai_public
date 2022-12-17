let bakResTxt = '';
function chkGameState(data) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = () => {
        if (xhr.readyState == 4 && xhr.status == 200) { // 通信の完了時 かつ 通信成功時
            if (xhr.responseText !== bakResTxt && bakResTxt !== '') {
                submitPage(xhr.responseText); // 遷移先が異なるため、各ページのJSに定義しておくこと。。
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
    'fnc_name' : 'chkGameState',
};

let watchAdminOperation = setInterval(function(){chkGameState(watchGameStateData)}, 1000);