let xhrLogin = new XMLHttpRequest();
let xhrNotLogin = new XMLHttpRequest();

let numOfLoginMembersTag = document.getElementById('js_num_of_login_members');
let loginMembersTableTag = document.getElementById('js_login_members_table');
let numOfLoginAndAbsenceMembersTag = document.getElementById('js_num_of_login_and_absence_members');

let numOfLoginMembersTableHeaderTag = document.getElementById('js_num_of_login_members_table_header');

// ログイン済メンバー表の列数
let numOfLoginMemberColumn = 4;

/* ログイン済メンバー情報を取得-表示 */
function fetchLoginMembersOnAjax() {
    let loginMembersTable = '';

    let numOfLoginAndAbsenceMembers = 0; // 欠席の連絡があったがログインが確認された方
    xhrLogin.onreadystatechange = function() {
        if (xhrLogin.readyState == 4 && xhrLogin.status == 200) {
            let loginMenbersArray = JSON.parse(xhrLogin.responseText);

            // ログイン済メンバーの人数表示
            numOfLoginMembersTag.innerText = loginMenbersArray.length;
            numOfLoginMembersTableHeaderTag.innerText = loginMenbersArray.length;

            let columnNo = 1;
            loginMenbersArray.forEach(element => {

                // 欠席連絡があったがログインしたメンバーを計上
                let absenceMember = '';
                if (element.absence_flg === '1') {
                    absenceMember = '※ ';
                    numOfLoginAndAbsenceMembers = Number(numOfLoginAndAbsenceMembers) + 1;
                }

                // ログイン済メンバーの一覧(table)作成
                if (columnNo === 1) {
                    loginMembersTable += '<tr>';
                }
                loginMembersTable += `<td class="border-collapse border border-gray-400">${absenceMember}${element.member_name}</td>`;

                if (columnNo >= numOfLoginMemberColumn) {
                    loginMembersTable += '</tr>';
                    columnNo = 1;
                } else {
                    columnNo = Number(columnNo) + 1;
                }
            });

            if (columnNo !== 1) {
                for (i = columnNo; i <= numOfLoginMemberColumn; i++) {
                    loginMembersTable += '<td class="border-collapse border border-gray-400"></td>';
                }
                loginMembersTable += '</tr>';
            }

            // ログインメンバーの一覧テーブルを表示
            loginMembersTableTag.innerHTML = loginMembersTable;

            // 欠席連絡済でログイン済のメンバー人数を表示
            numOfLoginAndAbsenceMembersTag.innerText = numOfLoginAndAbsenceMembers;
        }
    }

    xhrLogin.open('POST', './Ajax.php');
    xhrLogin.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhrLogin.send(encodeHtmlForm({'fnc_name':'fetchQuizLoginMembers', 'login_state': '1'}));
}

/* 未ログインメンバー情報を取得-表示 */
let notLoginMembersTableTag = document.getElementById('js_not_login_members_table');
let numOfNotLoginMembersTableHeaderTag = document.getElementById('js_num_of_not_login_members_table_header');


function fetchNotLoginMembersOnAjax() {
    let notLoginMembersTable = '';

    xhrNotLogin.onreadystatechange = function() {
        if (xhrNotLogin.readyState == 4 && xhrNotLogin.status == 200) {
            let notLoginMenbersArray = JSON.parse(xhrNotLogin.responseText);

            // 未ログインメンバーの人数表示
            numOfNotLoginMembersTableHeaderTag.innerText = notLoginMenbersArray.length;

            notLoginMenbersArray.forEach(element => {
                // 未ログインメンバーの一覧(table)作成
                notLoginMembersTable += `<tr><td class="border-collapse border border-gray-400">${element.member_name}</td></tr>`;
            });

            // 未ログインメンバーの一覧テーブルを表示
            notLoginMembersTableTag.innerHTML = notLoginMembersTable;
        }
    }

    xhrNotLogin.open('POST', './Ajax.php');
    xhrNotLogin.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhrNotLogin.send(encodeHtmlForm({'fnc_name':'fetchQuizNotLoginMembers', 'login_state':'1'}));
}

fetchLoginMembersOnAjax();
fetchNotLoginMembersOnAjax();

setInterval( () => {
    fetchLoginMembersOnAjax();
    fetchNotLoginMembersOnAjax();
}, 1000);