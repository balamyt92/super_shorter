const Fingerprint2 = require('fingerprintjs2');

function calcFingerPrint(cb) {
    if (window.requestIdleCallback) {
        requestIdleCallback(function () {
            Fingerprint2.get(function (components) {
                cb(makeFingerPrint(components))
            })
        })
    } else {
        setTimeout(function () {
            Fingerprint2.get(function (components) {
                cb(makeFingerPrint(components))
            })
        }, 500)
    }

    function makeFingerPrint(components) {
        return Fingerprint2.x64hash128(components.map(function (pair) {
            return pair.value
        }).join(), 31);
    }
}


function getFingerPrint(cb) {
    let myFingerPrint = localStorage.getItem('super_shorter_fingerprint');
    if (myFingerPrint) {
        cb(myFingerPrint);
        return;
    }
    calcFingerPrint(function (fingerPrint) {
        localStorage.setItem('super_shorter_fingerprint', fingerPrint);
        cb(fingerPrint);
    });
}

module.exports = getFingerPrint;
