var config = {
    "map":{
      '*': {
          'Az2009_Cielo/js/cc/validate': 'Az2009_Cielo/js/cc/validate'
      }
    },
    shim: {
        'Az2009_Cielo/js/cc/validate': {
            deps: [
                'jquery',
                'jquery/validate',
            ]
        }
    }
};