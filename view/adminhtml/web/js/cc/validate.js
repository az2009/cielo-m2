requirejs([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'Az2009_Cielo/js/model/credit-card-validation/credit-card-number-validator'
], function($, ui, validate, cardNumberValidator){

    define([
        'jquery',
        'jquery/ui',
        'jquery/validate',
        'Az2009_Cielo/js/model/credit-card-validation/credit-card-number-validator'
    ], function($, ui, validate, cardNumberValidator){
        'use strict';
        return function() {
            $.validator.addMethod(
                "validate-credit-card",
                function(value, element) {
                    var result = cardNumberValidator(value);
                    return result.isValid;
                },

                $.mage.__("Invalid Credit Card")
            );
        }

    });

});
