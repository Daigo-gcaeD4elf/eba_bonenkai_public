function encodeHtmlForm(data)
{
    let params = [];

    for (let name in data) {
        let value = data[name];
        let param = encodeURIComponent(name) + '=' + encodeURIComponent(value);

        params.push(param);
    }

    return params.join( '&' ).replace( /%20/g, '+' );
}