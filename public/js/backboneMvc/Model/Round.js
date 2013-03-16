window.Round = Backbone.Model.extend({
    
    initialize: function() {
        
    },
    
    
    urlRoot: "/round-rest"
    
});

window.Rounds = Backbone.Collection.extend({
    
    initialize: function() {
        
    },
    
    url: "/round-rest"
    
});



