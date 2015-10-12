function loadTeamSettings() {
    var furl = [location.protocol, '//', location.host, location.pathname].join('');
    var shrtName = document.getElementById('jg_team_id').value;
        
    var re = new RegExp("([?&])shrt"=.*?(&|$)", "i");
    var separator = furl.indexOf('?') !== -1 ? "&" : "?";
    if (furl.match(re)) {
        furl = furl.replace(re, '$1' + "shrt=" + shrtName + '$2');
    }
    else {
        furl = furl + separator + "shrt=" + shrtName;
    }    
    window.location.href = furl;    
}

