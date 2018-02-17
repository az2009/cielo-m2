/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/translate',
        'Az2009_Cielo/js/model/credit-card-validation/credit-card-data',
        'Az2009_Cielo/js/model/credit-card-validation/credit-card-number-validator',
        'Az2009_Cielo/js/model/credit-card-validation/validate-docnumber'
    ],
    function ($, Component, $t, creditCardData, cardNumberValidator, validateCpfCnpj) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Az2009_Cielo/payment/form',
                timeoutMessage: $t('Sorry, but something went wrong. Please contact the seller.'),
                creditCardNumber: '',
                creditCardType: '',
                selectedCardType: '',
                docNumber : '',
                messageValidateDoc: ''
            },

            initObservable: function () {

                this._super().observe([
                    'creditCardNumber',
                    'creditCardType',
                    'selectedCardType',
                    'docNumber',
                    'messageValidateDoc'
                ]);

                return this;
            },

            initialize: function() {

                this._super();
                var self = this;

                this.creditCardNumber.subscribe(function (value) {
                    var result;

                    if (value.length > 16) {
                        value = value.substr(0, 16);
                        self.creditCardNumber(value);
                    }

                    self.selectedCardType(null);
                    if (value === '' || value === null) {
                        return false;
                    }

                    result = cardNumberValidator(value);
                    if (!result.isPotentiallyValid && !result.isValid) {
                        return false;
                    }

                    if (result.card !== null) {
                        self.selectedCardType(result.card.type);
                        creditCardData.creditCard = result.card;
                    }

                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                    }
                });

                this.docNumber.subscribe(function(value){
                    var response;
                    if (response = validateCpfCnpj(value)) {
                        self.messageValidateDoc(response.message);
                    }
                });
            },

            getCode: function() {
                return 'az2009_cielo';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_number': this.creditCardNumber,
                        'cc_type': this.creditCardType(),
                    }
                };
            },

            isShowLegend: function () {
                return true;
            },

            isAvailable: function () {
                return true;
            },

            /**
             * Get list of available credit card types values
             * @returns {Object}
             */
            getCcAvailableTypesValues: function () {
                return _.map(this.getCcAvailableTypes(), function (value, key) {
                    return {
                        'value': key,
                        'type': value
                    };
                });
            },

            /**
             * Get list of available credit card types
             * @returns {Object}
             */
            getCcAvailableTypes: function () {
                return window.checkoutConfig.payment.az2009_cielo.availableTypes;
            },

            /**
             * Get payment icons
             * @param {String} type
             * @returns {Boolean}
             */
            getIcons: function (type) {
                return window.checkoutConfig.payment.az2009_cielo.icons.hasOwnProperty(type) ?
                    window.checkoutConfig.payment.az2009_cielo.icons[type]
                    : false;
            },

            isPlaceOrderActionAllowed: function(value) {
                return false;
            }

        });
    });