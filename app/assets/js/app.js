/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import getFingerPrint from './src/fingerprint';

require('../../node_modules/bootstrap/dist/css/bootstrap.min.css');
require('../css/app.css');
require('../../node_modules/bootstrap/dist/js/bootstrap.min');

const $ = require('jquery');

$(function () {
    let $shorter_form = $('.js-shorter-form');

    if ($shorter_form.length) {
        getFingerPrint(function (fingerprint) {
            $shorter_form.find('input[name=fingerprint]').val(fingerprint);
        });

        let old_url = null,
            $urlInput = $shorter_form.find('input[name=url]');
        $shorter_form.on('submit', function (e) {
            e.preventDefault();

            if (old_url === $urlInput.val()) {
                return false;
            }
            old_url = $urlInput.val();

            let data = new FormData($shorter_form[0]);
            let xhr = new XMLHttpRequest();
            let url = $shorter_form.attr('action');

            xhr.open('POST', url);
            xhr.onload = function () {
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
        $error_alert = $('.js-error-alert'),
        $old_link = $('.js-old-links');

    if ($old_link.length) {
        renderOldLinks()
    }

    let $commercial = $('.js-commercial');
    if ($commercial.length) {
        let $img = $commercial.find('img');
        $img.on('load', function () {
            let start = new Date().getTime(),
                distance = 5000,
                renderSpan = $commercial.find('.js-seconds');
            setInterval(function () {
                let countdown = new Date().getTime() - start;
                if (countdown >= distance) {
                    window.location = $commercial.data('link');
                    return;
                }
                renderSpan.text(Math.floor((distance - countdown) / 1000));
            }, 200);

            $.post('/statistic/add/image', {
                image: $commercial.data('image')
            });
        });
        $img.attr('src', $commercial.data('image'))
    }

    function renderOldLinks() {
        let old_link = getLinksFromLocalStorage();
        if (old_link.length) {
            let html = '';
            for (let oldLinkKey in old_link) {
                let link = makeLink(old_link[oldLinkKey]),
                    statistic_link = makeStatisticLink(old_link[oldLinkKey]);
                html += `<li class="list-group-item"><a href="${link}" target="_blank">${link}</a> (<a href="${statistic_link}">statistic</a>)</li>`;
            }
            console.log($old_link);
            $old_link.find('ul').html(html);
            $old_link.show();
        }
    }

    function renderSuccess(resp) {
        $error_alert.hide();
        let link = makeLink(resp.link),
            statistic_link = makeStatisticLink(resp.link);

        $success_alert
            .html(`Your link <a href="${link}" target="_blank">${link}</a> (<a href="${statistic_link}">statistic</a>)`)
            .show();
        addLink(resp.link);
    }

    function getLinksFromLocalStorage() {
        let links = localStorage.getItem('super_shorter_link');
        if (links) {
            try {
                links = JSON.parse(links);
            } catch (e) {
                links = [];
            }
        } else {
            links = [];
        }
        return links;
    }

    function saveLinksToLocalStorage(links) {
        localStorage.setItem('super_shorter_link', JSON.stringify(links));
    }

    function addLink(link) {
        let links = getLinksFromLocalStorage();
        links.push(link);
        saveLinksToLocalStorage(links);
        renderOldLinks();
    }

    function renderError(resp) {
        $success_alert.hide();
        $error_alert.html(resp.message).show();
    }

    function makeLink(uri) {
        return $shorter_form.data('host') + uri;
    }

    function makeStatisticLink(uri) {
        return $shorter_form.data('stat') + uri;
    }
});
