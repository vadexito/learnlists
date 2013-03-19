var learnMVC = new Marionette.Application();

learnMVC.addRegions({
    answer  : '#question_asked_answer',
    text    : '#question_asked_text',
    results : '#round-results'
});

learnMVC.addInitializer(function(){  
  new QuestionView({model:new Question()});
});