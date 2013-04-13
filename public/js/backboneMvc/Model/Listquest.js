window.Listquest= Backbone.Model.extend({
    
    initialize: function() {
        
    },
    
    defaults: {
        title: 'undefined',
        questions : 'undefined'
    },
    
    urlRoot: "/learnlists-rest/listquest"
    
});



