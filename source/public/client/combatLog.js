window.combatLog = {

    onTurnStart: function(){
        $('.logentry').remove();
    },
    
    logDestroyedShip: function(ship){
    
        var html = '<div class="logentry"><span class="destroyed">';
        
        // When the name is only a number, it might not be interpreted as a string.
        // In that case, the toUpperCase goes wrong.
        // Make certain the name is a string.
        if(typeof ship.name == 'string' || ship.name instanceof String){
            html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name.toUpperCase() + '</span> DESTROYED</span>';
        }else{
            html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name + '</span> DESTROYED</span>';
        }
            
        $(html).prependTo("#log");
    },
    
    logFireOrders: function(orders){
        //fire.x != "null" && otherFire.x == fire.x && fire.y != "null"
        var count = 0;
        var ship = gamedata.getShip(orders[0].shooterid);
        var target = gamedata.getShip(orders[0].targetid);
        var shots = 0;
        var shotshit = 0;
        var shotsintercepted = 0;
        var damages = Array();
        var lowC = 100000;
        var highC = 0;
        var notes = "";
        
        for (var a in orders){            
                        
            count++;
            var fire = orders[a];
            
            var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
            shots += fire.shots;
            shotshit += fire.shotshit;
            shotsintercepted += fire.intercepted;
            weaponManager.getDamagesCausedBy(damages, fire);
            var needed = fire.needed;
            if (needed < 0)
				needed = 0;
				
            if (needed < lowC)
                lowC = needed;
            if (needed >highC)
                highC = needed;
                
            if (fire.pubnotes)
                notes += fire.pubnotes + " ";
                        
        }
            
        
        var html = '<div class="logentry"><span class="logheader fire">FIRE: </span><span>';
            html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name + '</span>';   
            
            var counttext = (count>1) ? count+"x " : "";
            var chancetext = "";
            if (lowC == highC)
                chancetext = "Chance to hit: " + lowC + "%";
            else
                chancetext = "Chance to hit: " + lowC + "% - " +highC+"%";
                
            if (!target)
                chancetext = "";
                
            var intertext = "";
            if (shotsintercepted>0)
                intertext = ", " +shotsintercepted + " intercepted"
                
            var targettext = "";
            if (target)
                targettext = '<span> at </span><span class="shiplink target" data-id="'+target.id+'" >' + target.name + '</span>';
            
            var shottext = "";
            if (target)
                shottext = ', '+shotshit+'/'+shots+' shots hit'+intertext+'.';
                
            var notestext = "";
            if (notes)
               notestext = '<span class="pubotes">'+notes+'</span>';
            
            if(mathlib.arrayIsEmpty(weapon.missileArray)){
                html += ' firing ' +counttext + weapon.displayName + targettext+'. '+chancetext +shottext + notestext;
            }else{
                html += ' firing ' +counttext + weapon.missileArray[weapon.firingMode].displayName + targettext+'. '+chancetext +shottext + notestext;
            }
        
            
        
                    
    
        
        
        html += '<span class="notes"> '+fire.notes+'</span>';
    //  html += damagehtml;
        html+='</span></div>';
        
        if (damages.length > 0){
            html += "<ul>";
           
            for (var i in damages){
                var victim = damages[i].ship;
                var totaldam = 0; 
                var armour = 0;
                var damagehtml = "";
                for (var a in damages[i].damages){
                    var d = damages[i].damages[a];
                    if (d.damage-d.armour<=0)
                        continue;
                        
                    totaldam += d.damage-d.armour;
                    armour += d.armour;
                    var system = shipManager.systems.getSystem(gamedata.getShip(d.shipid), d.systemid); 
                    
                    if (!d.destroyed){
                        continue;
                    }
                    
                    var first = "";
                    var comma = ",";
                    if (damagehtml.length == 0){
                        first = " Systems destroyed: "
                        comma = "";
                    }
                    
                    damagehtml += first + '<span class="damage">'+comma+' '+shipManager.systems.getDisplayName(system)+'</span>'
                    
                }
                
                if (totaldam > 0){
          //          html += '<li><span class="shiplink victim" data-id="'+ship.id+'" >' + victim.name + '</span> damaged for ' + totaldam + '(+ ' + armour + ' armour). '+ damagehtml+'</li>';   
            
                    html += '<li><span class="shiplink victim" data-id="'+ship.id+'" >' + victim.name + '</span> damaged for ' + totaldam + ' (total armour mitigation: ' + armour + ').</li>';
                    if (damagehtml.length > 1){
                        html += '<li>' + damagehtml + '</li>';
                    }
                }
                
            }
            
            html += "</ul>";
        }
        
            
        $(html).prependTo("#log");
    },
            
        
    logAmmoExplosion: function(ship, system){

        var dmg;

        var damages = "Systems damaged: ";
        var destroyed = "Systems destroyed: ";

        if (system.displayName == "Bomb Rack"){
            dmg = 35;
        } else if (system.displayName == "Reload Rack"){
            dmg = 120;
        } else dmg = 70;


        for (var i = 0; i < ship.systems.length; i++){
            var sys = ship.systems[i];
            for (var j = 0; j < sys.damage.length; j++){
                var entry = sys.damage[j];
                if (entry.fireorderid == -1 && entry.turn == gamedata.turn){
                    if (entry.destroyed == 1){
                        destroyed += '<span class="damage">' + shipManager.systems.getDisplayName(sys) + '</span>';
                        destroyed += ', ';
                        break;
                    }
                    else {
                        damages += shipManager.systems.getDisplayName(sys);
                        damages += ', ';
                        break;
                    }
                }
            }
        }




        var html = '<div class="logentry">';
            html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name + '</span>';   
            html +=  ' suffered ' + dmg + ' damage due to exploding ammunition from its ' + system.displayName + '.';

            if (damages.length >15){
                var length = damages.length;
                damages = damages.substring(0, length-2);
                html +=  '<li>' + damages + '</li>';
            }
            if (destroyed.length >15){
                var length = destroyed.length;
                destroyed = destroyed.substring(0, length-2);
                html +=  '<li>' + destroyed + '</li>';
            }


            html +='</span></div></ul>';


        $(html).prependTo("#log");
    },
    
    logMoves: function(ship){
        
        var e = $('.logentry.'+ship.id +' .move.t'+gamedata.turn);
        if (e.length > 0)
            return;
        
        var start = shipManager.movement.getFirstMoveOfTurn(ship);
        var end = shipManager.movement.getLastCommitedMove(ship);
        
        if (!start || !end)
            return;
        
        var html = '<div class="logentry '+ship.id+'" data-shipid="'+ship.id+'"><span class="logheader move t'+gamedata.turn+'">MOVE: </span> <span class="shiplink" data-id="'+ship.id+'" >' + ship.name + '</span>';
            html += '<span> From ('+start.x+','+start.y+') to ('+end.x+','+end.y+') </span></div>';
        var log = $(html);
        //var details = $('<ul><li><span> From ('+start.x+','+start.y+') to ('+end.x+','+end.y+') </span></ul></li>')
        
        log.on('click', animation.replayMoveAnimation);
        //$(details).prependTo("#log");
        $(log).prependTo("#log");
        
        
    }
    

}
