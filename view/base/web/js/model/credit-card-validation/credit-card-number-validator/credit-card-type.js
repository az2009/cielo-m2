/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'jquery',
    'mageUtils'
], function ($, utils) {
    'use strict';

    var types = [
        {
            title: 'Elo',
            type: 'ELO',
            pattern: '^(?:401178|401179|431274|438935|451416|457393|457631|457632|504175|627780|636297|636368|655000|655001|651652|651653|651654|650485|650486|650487|650488|506699|5067[0-6][0-9]|50677[0-8]|509\\d{3})\\d{10}$',
            gaps: [],
            lengths: [16],
            code: {
                name: 'CVV',
                size: 3
            }
        },
        {
            title: 'Hipercard',
            type: 'HIC',
            pattern: '^606282|3841\\d{2}',
            gaps: [],
            lengths: [16],
            code: {
                name: 'CVV',
                size: 3
            }
        },
        {
            title: 'Aura',
            type: 'AUR',
            pattern: '^(5078\\d{2})(\\d{2})(\\d{11})$',
            gaps: [],
            lengths: [14, 16, 17, 18, 19],
            code: {
                name: 'CVV',
                size: 3
            }
        },
        {
            title: 'Visa',
            type: 'VI',
            pattern: '^4\\d{12}(\\d{3})?$',
            gaps: [4, 8, 12],
            lengths: [16],
            code: {
                name: 'CVV',
                size: 3
            }
        },
        {
            title: 'MasterCard',
            type: 'MC',
            pattern: '^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$',
            gaps: [4, 8, 12],
            lengths: [16],
            code: {
                name: 'CVC',
                size: 3
            }
        },
        {
            title: 'American Express',
            type: 'AE',
            pattern: '^3([47]\\d*)?$',
            isAmex: true,
            gaps: [4, 10],
            lengths: [15],
            code: {
                name: 'CID',
                size: 4
            }
        },
        {
            title: 'Diners',
            type: 'DN',
            pattern: '^(3(0[0-5]|095|6|[8-9]))\\d*$',
            gaps: [4, 10],
            lengths: [14, 16, 17, 18, 19],
            code: {
                name: 'CVV',
                size: 3
            }
        },
        {
            title: 'Discover',
            type: 'DI',
            pattern: '^(6011(0|[2-4]|74|7[7-9]|8[6-9]|9)|6(4[4-9]|5))\\d*$',
            gaps: [4, 8, 12],
            lengths: [16, 17, 18, 19],
            code: {
                name: 'CID',
                size: 3
            }
        },
        {
            title: 'JCB',
            type: 'JCB',
            pattern: '^35(2[8-9]|[3-8])\\d*$',
            gaps: [4, 8, 12],
            lengths: [16, 17, 18, 19],
            code: {
                name: 'CVV',
                size: 3
            }
        }
    ];

    return {
        /**
         * @param {*} cardNumber
         * @return {Array}
         */
        getCardTypes: function (cardNumber) {

            var i, value,
                result = [];

            if (utils.isEmpty(cardNumber)) {
                return result;
            }

            if (cardNumber === '') {
                return $.extend(true, {}, types);
            }

            for (i = 0; i < types.length; i++) {
                value = types[i];

                if (new RegExp(value.pattern).test(cardNumber)) {
                    if (!result.length) {
                        result.push($.extend(true, {}, value));
                    }
                }
            }

            return result;
        }
    };
});
