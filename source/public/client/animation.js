
window.animation = {

    movementspeed: 10,
    turningspeed: 15,
    waitingElement: null,
    animating:new Array(),
    shipAnimating:0,
    afterAnimationCallback: new Array(),
    animationloopdelay:0,
    gravMovesAnimated:false,
    
    animationLoop: function(){
        
        animation.animateActiveship();
        
        if (animation.animating.length > 0){
            if(animation.animationloopdelay > 0){
                animation.animationloopdelay--;
            }else{
                animation.animating[animation.animating.length-1]();
            }
        }
    
        window.requestAnimFrame(animation.animationLoop);
    },
    
    setAnimating: function(animatefunction, callback){
        //console.log("setAnimating");
        animation.animationloopdelay = 0;
        animation.animating.push(animatefunction);
        gamedata.animating = true;
        animation.afterAnimationCallback.push(callback);
    },
    
    endAnimation: function(){
        //console.log("endAnimating");
        gamedata.animating = false;
        animation.animating.pop();
        animation.shipAnimating = -1;
        if (animation.afterAnimationCallback.length > 0){
            // Construction needed to pop callback before actually calling it.
            tempCallback = animation.afterAnimationCallback[animation.afterAnimationCallback.length-1];
            animation.afterAnimationCallback.pop();
            tempCallback();
        }
    },
    
    animateActiveship: function(){

            if (gamedata.animating) {
                return false;
            }
                
            var ship = gamedata.getActiveShip();
            
            if (ship == null){
                ship = gamedata.getSelectedShip();
            }
            
            if (ship == null) {
                return;
            }

            for (var a in ship.movement){
                var movement = ship.movement[a];
                
                if (movement.animated == false){
                    if (!movement.commit) {
                        break;
                    }
                
                    if (animation.checkAnimationDone(movement)){
                        movement.animated = true;
                        gamedata.shipStatusChanged(ship);
                        ballistics.calculateBallisticLocations();
                        ballistics.drawBallistics();
                        shipManager.drawShip(ship);
                    }else{
                        ballistics.hideBallistics();
                        movement.animationtics ++;
                        shipManager.drawShip(ship);
                        break;
                    }
                }
            
            }

    },
    
    hasMoreforAnimate: function(ship, m){
        var found = false;
        for (var a in ship.movement){
            var movement = ship.movement[a];
            if (movement == m){
                found = true;
                continue;
            }
            
            if (found && movement.animated == false)
                return true;
            
        }
        
        return false;
    
    },
    
    animateShipMoves: function(){
    
        
        var done = false;
        var found = false;
        var shipchanged = false;
        var gravMovesFound = false;
        
        if(gamedata.gamephase == 3){
            animation.gravMovesAnimated = false;
        }
        
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
                    
            for (var a in ship.movement){
                var movement = ship.movement[a];
                
                // skip any gravitic movement during normal ship moves
                if(movement.type.charAt(0) == 'g'){
                    gravMovesFound = true;
                    continue;
                }
                
                if (movement.animated == false){
                    if (!movement.commit)
                        break;
                            
                    found = true;
                    if (animation.shipAnimating != ship.id){
                        animation.shipAnimating = ship.id
                        scrolling.scrollToShip(ship);
                        shipchanged = true;
                        combatLog.logMoves(ship);
                        
                    }
                    
                    if (animation.checkAnimationDone(movement)){
                        //console.log("animated: ship " +ship.name +" move: " +movement.type);
                        if (!animation.hasMoreforAnimate(ship, movement)){
                            done = true;
                        }
                        movement.animated = true;
                        gamedata.shipStatusChanged(ship);
                        ballistics.calculateBallisticLocations();
                        ballistics.drawBallistics();
                        shipManager.drawShip(ship);
                    }else{
                        //console.log(" - animating: ship " +ship.name +" move: " +movement.type);
                        ballistics.hideBallistics();
                        movement.animationtics ++;
                        shipManager.drawShip(ship);
                        break;
                    }
                }
            
            }
            
            if (found){
                if (done)
                    animation.animationloopdelay = 30;
                    
                break;
            }
            
        }
        
        if (!found) {
            //if(gravMovesFound){
            //    // Do the grav moves
            //    gamedata.phase = 31;
            //}

            animation.endAnimation();
        }
        
    },
    
    checkAnimationDone: function(movement){
    
        if ( movement.type=="move" || movement.type=="slipright" || movement.type=="slipleft"){
            return (movement.animationtics >= animation.movementspeed);
        }
        
        if (movement.type=="turnright" || movement.type=="turnleft" || movement.type=="pivotright" || movement.type=="pivotleft"){
            return (movement.animationtics >= animation.turningspeed);
        }

        return true;
    },

    animateShipGravMoves: function(){
    
        
        var done = false;
        var found = false;
        var shipchanged = false;

        // Needed to be done here to make certain the grav moves are animated
        // otherwise the method drawShip later in this function will not know
        // it's time to animate the grav moves.
        animation.gravMovesAnimated = true;
        
        // Go a bit slower for gravitic moves
        animation.movementspeed = 40;

        console.log("********* animateShipGravMoves ***********");

        for (var i in gamedata.ships){
            console.log("a");
            var ship = gamedata.ships[i];

            for (var a in ship.movement){
                console.log("b");
                var movement = ship.movement[a];
                
                // skip any non-gravitic movement during gravitic ship moves
                if(movement.type.charAt(0) != 'g'){
                    console.log("c");

                    continue;
                }
                
                if (movement.animated == false){
                    console.log("d");

                    if (!movement.commit){
                        console.log("e");
                        break;
                    }
                            
                    found = true;
                    if (animation.shipAnimating != ship.id){
                        console.log("f");

                        animation.shipAnimating = ship.id
                        scrolling.scrollToShip(ship);
                        shipchanged = true;
                        combatLog.logMoves(ship);
                        
                    }
                    
                    if (animation.checkGravAnimationDone(movement)){
                        console.log("g");

                        gamedata.activeship = -1;

                        //console.log("animated: ship " +ship.name +" move: " +movement.type);
                        if (!animation.hasMoreforAnimate(ship, movement)){
                            done = true;
                        }
                        movement.animated = true;
                        gamedata.shipStatusChanged(ship);
                        ballistics.calculateBallisticLocations();
                        ballistics.drawBallistics();
                        shipManager.drawShip(ship);
                    }else{
                        console.log("i");

                        //gamedata.activeship = ship;
                        gamedata.selectShip(ship, false);
                        scrolling.scrollToShip(ship);
                        ship.drawn = false;

                        //console.log(" - animating: ship " +ship.name +" move: " +movement.type);
                        ballistics.hideBallistics();
                        movement.animationtics ++;
                        shipManager.drawShip(ship);
                        break;
                    }
                }
            
            }
            
            if (found){
                if (done)
                    animation.animationloopdelay = 30;
                    
                break;
            }
            
        }
        
        if (!found){
            animation.movementspeed = 10;
            animation.endAnimation();
        }
        
        
    },

    checkGravAnimationDone: function(movement){
    
        if ( movement.type=="gslip"){
            return (movement.animationtics >= animation.movementspeed);
        }
        
        if (movement.type=="gpivot"){
            return (movement.animationtics >= animation.turningspeed);
        }

        return true;
    },

    animateWaiting: function(){
        if (animation.waitingElement == null){
            animation.waitingElement  = $("#phaseheader .waiting.value");
            animation.waitingElement.data("dots", 0);
            }
            
        var e = animation.waitingElement;
        var dots = e.data("dots");
        
        dots++;
        
        if (dots > 3)
            dots = 0;
        
        s = "";
        if (dots == 3)
            s = "...";
        if (dots == 2)
            s = "..";
        if (dots == 1)
            s = ".";            
        
        e.html("WAITING FOR TURN"+s);
        e.data("dots", dots);
    },
    
    cancelAnimation: function(){
        if (!gamedata.animating)
            return; 
        
        animation.endAnimation();
        
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            var found = false;
            for (var a in ship.movement){
                var move = ship.movement[a];
                
                if (move.commit !== true)
                    continue;
                
                if (!move.animated){
                    found = true;
                    move.animated = true;
                    move.animating = false;
                }
            }
            
            if (found)
                combatLog.logMoves(ship);
            
            gamedata.shipStatusChanged(ship);
            shipManager.drawShip(ship);
            
        }
        ballistics.calculateBallisticLocations();
        ballistics.drawBallistics();
        
       
    },
    
    replayMoveAnimation: function(e){
        if (gamedata.animating)
            return; 
        
        e.stopPropagation();
        var id = $(this).data("shipid");
        var ship = gamedata.getShip(id);
        
        for (var i in ship.movement){
            var move = ship.movement[i];
            if (move.turn !== gamedata.turn || move.type == "start" || move.type == "deploy")
                continue;
            
            move.animating = false;
            move.animated = false;
            move.animationtics = 0;
        }
        UI.shipMovement.hide();
        animation.setAnimating(animation.animateShipMoves, animation.resumeFromReplay);
    },
    
    resumeFromReplay: function(){
    }
    


}
