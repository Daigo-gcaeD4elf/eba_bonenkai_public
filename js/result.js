let refinfo = document.referrer;
if (!refinfo) {
    alert('直リンクはご遠慮下さいませ(;・∀・)');
}

function submitPage(res) {
    // let state = res.slice(-1);
    // if (state === '0') {
    //     document.finishGame.submit();
    // } else {
        document.nextGame.submit();
    // }
}

