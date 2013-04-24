var learnMVC = new Marionette.Application();

learnMVC.addRegions({
    main:'#questionMain'
});

learnMVC.addInitializer(function(options){
    
    var learnMain = new LearnMain({
        listId:options.listId,
        loggedIn: options.loggedIn,
        maxRound: options.maxRound,
        saveRoundsWhenNotLogged : this.saveRoundsWhenNotLogged
    }); 
    
    learnMVC.initLayouts = function(){
        var layout = new LearnListsLayout({model:learnMain}); 
        learnMVC.layout = layout;
        learnMVC.main.show(layout);  

        var topRegionLayout = new TopRegionLayout({model:learnMain});    
        var mainRegionLayout = new MainRegionLayout({model:learnMain});    
        var sideRegionLayout = new SideRegionLayout({model:learnMain}); 

        layout.top.show(topRegionLayout);
        layout.main.show(mainRegionLayout);
        layout.side.show(sideRegionLayout);

        var titleListView = new TitleListView({model:learnMain});
        var roundNumberView = new RoundNumberView({model:learnMain});
        var timerView = new TimerView({model:learnMain}); 
        var questionView = new QuestionView({model:learnMain});
        var answerView = new AnswerView({model:learnMain});
        var commentView = new CommentView({model:learnMain});
        var inputView = new InputView({model:learnMain});
        var mainButtonsView = new MainButtonsView({model:learnMain});
        var questionFollowerView = new QuestionFollowerView({model:learnMain});
        var scoreView = new ScoreView({model:learnMain});
        var checkMessageView = new CheckMessageView({model:learnMain});
        var sideButtonsView = new SideButtonsView({model:learnMain});


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
    
    learnMVC.vent.on('learn:pre-initNewRound',learnMVC.initLayouts);
    learnMVC.vent.on('learn:showResult',function(){        
        learnMVC.layout.main.show(new ResultsView({model: learnMain}));
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