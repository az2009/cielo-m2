/*jshint jquery:true*/
define(
    [
        'jquery',
        'mage/url'
    ],
    function ($, urlBuilder) {
        'use strict';
        return function () {
            var url = urlBuilder.build('cielo/authenticate/index');
            $.mage.redirect(url);
        };
    }
);