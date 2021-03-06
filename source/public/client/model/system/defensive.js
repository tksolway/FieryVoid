
var InterceptorMkI = function(json, ship)
{
    Weapon.call( this, json, ship);
    this.defensiveType = "Interceptor";
}
InterceptorMkI.prototype = Object.create( Weapon.prototype );
InterceptorMkI.prototype.constructor = InterceptorMkI;

InterceptorMkI.prototype.getDefensiveHitChangeMod = 
    function(target, shooter, pos)
{
    return shipManager.systems.getOutput(target, this);
}

var InterceptorMkII = function(json, ship)
{
    InterceptorMkI.call( this, json, ship);
}
InterceptorMkII.prototype = Object.create( InterceptorMkI.prototype );
InterceptorMkII.prototype.constructor = InterceptorMkII;

var InterceptorPrototype = function(json, ship)
{
    InterceptorMkI.call( this, json, ship);
}
InterceptorPrototype.prototype = Object.create( InterceptorMkI.prototype );
InterceptorPrototype.prototype.constructor = InterceptorPrototype;

var Shield = function(json, ship)
{
    ShipSystem.call( this, json, ship);
    this.defensiveType = "Shield";
}

Shield.prototype = Object.create( ShipSystem.prototype );
Shield.prototype.constructor = Shield;

Shield.prototype.getDefensiveHitChangeMod = 
    function(target, shooter, pos)
{
    if (shooter.flight && mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)
        return 0;
    
    return shipManager.systems.getOutput(target, this);
}

var EMShield = function(json, ship)
{
    Shield.call( this, json, ship);
    this.defensiveType = "Shield";
}

EMShield.prototype = Object.create( Shield.prototype );
EMShield.prototype.constructor = EMShield;

var GraviticShield = function(json, ship)
{
    Shield.call( this, json, ship);
    this.defensiveType = "Shield";
}

GraviticShield.prototype = Object.create( Shield.prototype );
GraviticShield.prototype.constructor = GraviticShield;

var ShieldGenerator = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}

ShieldGenerator.prototype = Object.create( ShipSystem.prototype );
ShieldGenerator.prototype.constructor = ShieldGenerator;

ShieldGenerator.prototype.onTurnOff = function(ship)
{
    for(var i in ship.systems){
        var system = ship.systems[i];
        if(system.name == 'graviticShield'){
            // Shut it down.
            system.power.push({id:null, shipid:ship.id, systemid:system.id, type:1, turn:gamedata.turn, amount:0});
            shipWindowManager.setDataForSystem(ship, system);
        }
    }
}

ShieldGenerator.prototype.onTurnOn = function(ship)
{
    for(var i in ship.systems){
        var system = ship.systems[i];
        if(system.name == 'graviticShield'){
            // Turn it all on.
            shipManager.power.setOnline(ship, system);
            shipWindowManager.setDataForSystem(ship, system);
        }
    }
}


