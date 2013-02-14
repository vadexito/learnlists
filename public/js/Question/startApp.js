tpl.loadTemplates(['question'], function () {
    
    $(function() {

        new QuestionView({model:new Question()});
    });
});









