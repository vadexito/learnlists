var learnMVC = new Marionette.Application();

learnMVC.addRegions({
    main:'#questionMain'
});
learnMVC.addInitializer(function(options){
    learnMVC.initLayouts = function(model){
        var layout = new LearnListsLayout({model:model}); 
        learnMVC.layout = layout;
        learnMVC.main.show(layout);  

        var topRegionLayout = new TopRegionLayout({model:model});    
        var mainRegionLayout = new MainRegionLayout({model:model});    
        var sideRegionLayout = new SideRegionLayout({model:model}); 

        layout.top.show(topRegionLayout);
        layout.main.show(mainRegionLayout);
        layout.side.show(sideRegionLayout);

        var titleListView = new TitleListView({model:model});
        var roundNumberView = new RoundNumberView({model:model});
        var timerView = new TimerView({model:model}); 
        var questionView = new QuestionView({model:model});
        var answerView = new AnswerView({model:model});
        var commentView = new CommentView({model:model});
        var inputView = new InputView({model:model});
        var mainButtonsView = new MainButtonsView({model:model});
        var questionFollowerView = new QuestionFollowerView({model:model});
        var scoreView = new ScoreView({model:model});
        var checkMessageView = new CheckMessageView({model:model});
        var sideButtonsView = new SideButtonsView({model:model});


        topRegionLayout.left.show(titleListView);
        topRegionLayout.center.show(roundNumberView);
        topRegionLayout.right.show(questionFollowerView);

        mainRegionLayout.question.show(questionView);
        mainRegionLayout.answer.show(answerView);
        mainRegionLayout.comment.show(commentView);
        mainRegionLayout.input.show(inputView);
        mainRegionLayout.main_buttons.show(mainButtonsView);

        sideRegionLayout.top.show(timerView);
        sideRegionLayout.middle_top.show(checkMessageView);
        sideRegionLayout.middle.show(scoreView);
        sideRegionLayout.bottom.show(sideButtonsView);
        
        learnMVC.topLayout = topRegionLayout;
        learnMVC.mainLayout = mainRegionLayout;
        learnMVC.sideLayout = sideRegionLayout;
        
        console.log('init layouts');
    }
});

        
learnMVC.addInitializer(function(options){

    var model = new LearnMain({
        listId:options.listId,
        loggedIn: options.loggedIn,
        maxRound: options.maxRound,
        saveRoundsWhenNotLogged : this.saveRoundsWhenNotLogged
    });

    $('#start-modal').modal();
    $('#start-learn-btn').click(function(){
        console.log('trigger learn:start');
        learnMVC.vent.trigger("learn:start");
    });
    
        
    $('#seeHowItWorks-btn').click(function(){
        $('#start-modal').modal('hide');
        var optionsDemo = {
            text:'Text of the question',
            title_list:'Title of the list',
            comment:'Comment area for the teacher',
            round_nb:'4',
            round_total:'5',
            nb_question:'5',
            nb_questions:'20',
            score:'24 points',
            checkMessageTitle:'Excellent',
            checkMessage:'You are becoming really better and better',
            maxPoint:'25',
            comments:'+4 points for your quick and right answer'
        };
        model.set(optionsDemo);
        learnMVC.initLayouts(model);
        
        introJs().oncomplete(function() { 
            learnMVC.layout.close();
            learnMVC.vent.trigger("learn:start");
        }).onexit(function() {
            learnMVC.layout.close();
            learnMVC.vent.trigger("learn:start");
        }).start();
        
    });
    
    learnMVC.vent.on('learn:pre-initNewRound',function(){
        learnMVC.initLayouts(model);
    });
    learnMVC.vent.on('learn:showResult',function(){        
        learnMVC.layout.main.show(new ResultsView({model: model}));
        learnMVC.sideLayout.top.close();
    });
    
});

$(function(){
    
    learnMVC.start({
            listId:$('#listId').val(),
            loggedIn: $('#listId').attr('data-loggedin'),
            maxRound: $('#listId').attr('data-maxRound'),
            saveRoundsWhenNotLogged: false
    });
});