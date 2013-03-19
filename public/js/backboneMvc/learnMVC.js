var learnMVC = new Marionette.Application();

learnMVC.addRegions({
    answer      : '#question_asked_answer',
    text        : '#question_asked_text',
    results     : '#round-results',
    follower    : '#question_follower',
    currentstats: '#current_stats'
});


learnMVC.addInitializer(function(){  
    
    new QuestionView({model:new Question()});    
    
    var currentStats = new CurrentStats();
    learnMVC.follower.show(new FollowerView({model:currentStats}));
    learnMVC.currentstats.show(new CurrentStatsView({model:currentStats}));
});