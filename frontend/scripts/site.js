function switchLang(lang) {
    var expire = new Date();
    expire.setTime(expire.getTime() + 365 * 24 * 3600 * 1000);
    document.cookie = "locale=" + lang + "; path=/; expires=" + expire.toGMTString();
    window.location.reload();
}