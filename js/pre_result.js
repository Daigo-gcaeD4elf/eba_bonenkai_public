let refinfo = document.referrer;
if (!refinfo) {
    alert('直リンクはご遠慮下さいませ(;・∀・)');
}

let submitPage = setTimeout(submitResultPage, 8000);

function submitResultPage() {
    document.toResult.submit();
}
