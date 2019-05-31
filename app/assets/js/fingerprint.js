import getFingerPrint from './src/fingerprint';

document.addEventListener('DOMContentLoaded', function () {
    function postFingerprint(fingerprint) {
        let xhr = new XMLHttpRequest();

        xhr.open('POST', '/add-fingerprint', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            document.location.reload();
        };

        xhr.onerror = function () {
            alert('Ошибка');
        };

        xhr.setRequestHeader('X-REQUESTED-WITH', 'XMLHttpRequest');
        xhr.send('fingerprint=' + encodeURIComponent(fingerprint));
    }

    getFingerPrint(postFingerprint);
});
