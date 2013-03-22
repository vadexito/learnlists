window.CurrentStatsView = Backbone.Marionette.ItemView.extend({
    template: "#current_stats-template",
    tagName: 'span',
    ui:{
        perfectbar : '#perfect_answering_bar',
        average_bar : '#average_answering_bar',
        bad_bar     : '#bad_answering_bar'
    },
    initialize: function(){
        
        var optionsNotRefreshed = {transition_delay: 0};
        var optionsRefreshed = {transition_delay: 50};
        
        this.listenTo(this.model,'change:nb_perfect_answering',function(){
            this.render();
            this.ui.perfectbar.progressbar(optionsRefreshed);
            this.ui.average_bar.progressbar(optionsNotRefreshed);
            this.ui.bad_bar.progressbar(optionsNotRefreshed);
        });
        this.listenTo(this.model,'change:nb_average_answering',function(){
            this.render();
            this.ui.perfectbar.progressbar(optionsNotRefreshed);
            this.ui.average_bar.progressbar(optionsRefreshed);
            this.ui.bad_bar.progressbar(optionsNotRefreshed);
        });
        this.listenTo(this.model,'change:nb_bad_answering',function(){
            
            this.render();
            this.ui.perfectbar.progressbar(optionsNotRefreshed);
            this.ui.average_bar.progressbar(optionsNotRefreshed);
            this.ui.bad_bar.progressbar(optionsRefreshed);
        });
    }
});

