/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

require('../../node_modules/bootstrap/dist/css/bootstrap.min.css');
require('../css/app.css');
require('../../node_modules/bootstrap/dist/js/bootstrap.min');

const $ = require('jquery');

$(function () {
    let $shorter_form = $('.js-shorter-form');

    if ($shorter_form.length) {
        $shorter_form.on('submit', function (e) {
            e.preventDefault();

            let data = new FormData($shorter_form[0]);
            let xhr = new XMLHttpRequest();
            let url = $shorter_form.attr('action');

            xhr.open('POST', url);
            xhr.onload = function () {
                console.log(this);
                try {
                    if (this.status >= 200 && this.status < 400) {
                        renderSuccess(JSON.parse(this.response))
                    } else {
                        renderError(JSON.parse(this.response));
                    }
                } catch (e) {
                    renderError({message: 'Server error!'});
                }
            };

            xhr.onerror = function () {
                renderError({message: 'Server error!'})
            };

            xhr.setRequestHeader('X-REQUESTED-WITH', 'XMLHttpRequest');
            xhr.send(data);
        })
    }

    let $success_alert = $('.js-success-alert'),
        $error_alert = $('.js-error-alert');

    function renderSuccess(resp) {
        $error_alert.hide();
        let link = makeLink(resp.link);
        $success_alert.html(`Your link <a href="${link}" target="_blank">${link}</a>`).show();
    }

    function renderError(resp) {
        $success_alert.hide();
        $error_alert.html(resp.message).show();
    }

    function makeLink(uri) {
        return $shorter_form.data('host') + uri;
    }

});
