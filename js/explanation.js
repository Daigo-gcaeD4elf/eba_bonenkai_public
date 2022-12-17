let rps = document.getElementsByClassName('rps');

let refinfo = document.referrer;
if (!refinfo) {
    alert('直リンクはご遠慮下さいませ(;・∀・)');
}

function submitPage(res) {
    document.gameStart.submit();
}
