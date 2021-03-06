window.weaponManager = {
	mouseoverTimer: null,
	mouseOutTimer: null,
	mouseoverSystem: null,
	currentSystem: null,
	currentShip: null,

	getWeaponCurrentLoading: function(weapon)
	{
		if(weapon.duoWeapon){
			var returnArray = new Array(weapon.weapons[1].getTurnsloaded(), weapon.weapons[2].getTurnsloaded());
			return returnArray;
		}

		return weapon.turnsloaded;
	},

	onModeClicked: function(shipwindow, systemwindow, ship, system)
	{
		if (!system)
			return;

		if (gamedata.gamephase != 3 && !system.ballistic)
			return;

		if (gamedata.gamephase != 1 && system.ballistic)
			return;

		if (weaponManager.hasFiringOrder(ship, system))
			return;

		if (gamedata.isMyShip(ship)){
			weaponManager.unSelectWeapon(ship, system);

			if(system.dualWeapon){
				var parentSystem = shipManager.systems.getSystem(ship, system.parentId);
				parentSystem.changeFiringMode();
				shipWindowManager.setDataForSystem(ship, parentSystem);

				var newSystem = parentSystem.weapons[parentSystem.firingMode];

				var parentwindow = shipwindow.find(".parentsystem_"+newSystem.parentId);
				parentwindow.removeClass("system_"+system.id);
				parentwindow.addClass("modes");
				parentwindow.removeClass(system.name);
				shipWindowManager.addDualSystem(ship, parentSystem, parentwindow);
				shipWindowManager.setDataForSystem(ship, newSystem);
				parentwindow.find(".UI").addClass("active");
				parentwindow.find(".UI").focus();
				weaponManager.mouseoverSystem = parentwindow;
				clearTimeout(weaponManager.mouseOutTimer);
				clearTimeout(weaponManager.mouseoverTimer);
				weaponManager.mouseOutTimer = null;
				weaponManager.mouseoverTimer = null;
				systemInfo.showSystemInfo(parentwindow, newSystem, ship);
			}else{
				system.changeFiringMode();
				shipWindowManager.setDataForSystem(ship, newSystem);
			}


		}
	},

	onHoldfireClicked: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));

		if (gamedata.gamephase != 3 && !system.ballistic)
			return;

		if (gamedata.gamephase != 1 && system.ballistic)
			return;

		if (ship.userid == gamedata.thisplayer){
			if(!system.duoWeapon){
				weaponManager.cancelFire(ship, system);
			}else{
				systemwindow.removeClass("duofiring");

				for(var i in system.weapons){
					var duoweapon = system.weapons[i];

					if(weaponManager.hasFiringOrder(ship, duoweapon)){
						weaponManager.cancelFire(ship, duoweapon);
					}
				}
			}
		}

	},

	cancelFire: function(ship, system){
		weaponManager.removeFiringOrder(ship, system);
		ballistics.updateList();
		shipWindowManager.setDataForSystem(ship, system);
	},

	onWeaponMouseover: function(e){
		if (weaponManager.mouseOutTimer != null){
			clearTimeout(weaponManager.mouseOutTimer);
			weaponManager.mouseOutTimer = null;
		}

		if (weaponManager.mouseoverTimer != null)
			return;

		var id = $(this).data("shipid");
		weaponManager.currentShip = gamedata.getShip(id);

		if ($(this).hasClass("fightersystem")){
			weaponManager.currentSystem = shipManager.systems.getFighterSystem(weaponManager.currentShip, $(this).data("fighterid"), $(this).data("id"));
	}else{
			weaponManager.currentSystem = shipManager.systems.getSystem(weaponManager.currentShip, $(this).data("id"));
		}

		var targetElement = $(this);

			weaponManager.mouseoverSystem = targetElement;

		weaponManager.mouseoverTimer = setTimeout(weaponManager.doWeaponMouseOver, 150);
	},

	onWeaponMouseoverDuoSystem: function(e){
		// ignore this. We've already entered the parent of this duosystem
	},

	onWeaponMouseOutDuoSystem: function(e){
		// ignore this. We've already entered the parent of this duosystem
	},

	onWeaponMouseOut: function(e){
		//if($(this).is(weaponManager.mouseoverSystem) || $(this).is($(".UI"))){

		if (weaponManager.mouseoverTimer != null){
			clearTimeout(weaponManager.mouseoverTimer);
			weaponManager.mouseoverTimer = null;
		}

		weaponManager.mouseOutTimer = setTimeout(weaponManager.doWeaponMouseout, 50);
		//}
	},

	doWeaponMouseOver: function(e){
		if (weaponManager.mouseoverTimer != null){
			clearTimeout(weaponManager.mouseoverTimer);
			weaponManager.mouseoverTimer = null;
		}

		systemInfo.hideSystemInfo();
		weaponManager.removeArcIndicators();

		if( weaponManager.mouseoverSystem == null){
			return;
		}

		weaponManager.addArcIndicators(weaponManager.currentShip, weaponManager.currentSystem);
		systemInfo.showSystemInfo(weaponManager.mouseoverSystem, weaponManager.currentSystem, weaponManager.currentShip);

		drawEntities();
	},

	doWeaponMouseout: function(){
		if (weaponManager.mouseOutTimer != null){
			clearTimeout(weaponManager.mouseOutTimer);
			weaponManager.mouseOutTimer = null;
		}

		systemInfo.hideSystemInfo();

		weaponManager.mouseoverSystem = null;

		weaponManager.removeArcIndicators();
		drawEntities();

	},

	unSelectWeapon: function(ship, weapon){


		for(var i = gamedata.selectedSystems.length-1; i >= 0; i--){
			if(gamedata.selectedSystems[i] == weapon){
				gamedata.selectedSystems.splice(i,1);

			}

			if(weapon.duoWeapon){
				for(var j in weapon.weapons){
					var subweapon = weapon.weapons[j];

					weaponManager.unSelectWeapon(ship, subweapon);
				}
			}
		}

		shipWindowManager.setDataForSystem(ship, weapon);

	},

	checkConflictingFireOrder: function(ship, weapon, alert){

		var p = ship;
		if (ship.flight){
			p = shipManager.systems.getFighterBySystem(ship, weapon.id);
		}

		for (var i in p.systems){
			var system = p.systems[i];
			if (system.id == weapon.id)
				continue;

			if (weaponManager.hasFiringOrder(ship, system)){
				if (weapon.exclusive){
					if (alert)
						confirm.error("You cannot fire another weapon at the same time as " +weapon.displayName + ".");
					return true;
				}

				if (system.exclusive){
					if (alert)
						confirm.error("You cannot fire another weapon at the same time as " +system.displayName + ".");
					return true;
				}
			}
		}

		return false;
	},

	checkOutOfAmmo: function(ship, weapon){

		var p = ship;
		if (ship.flight){
			p = shipManager.systems.getFighterBySystem(ship, weapon.id);
		}else{
			return false;
		}

		if (weapon.hasOwnProperty("ammunition")){
			if (weapon.ammunition > 0){
				return false;
			}
			else {
				confirm.error("This fighter gun is out of ammunition.");
				return true;
			}
		}

		for (var i in p.systems){
			var system = p.systems[i];
			if (system.id != weapon.id)
				continue;

			if(system.missileArray){
				for(var j in system.missileArray){
					var missile = system.missileArray[j];

					if(missile.amount > 0){
						return false;
					}
					else {
						confirm.error("This missile rack is out of ammo.");
						return true;
					}
				}
			}
		}

		return false;
	},

	selectWeapon: function(ship, weapon){

		if(weaponManager.checkOutOfAmmo(ship, weapon)){
			return
		}

		if (weaponManager.checkConflictingFireOrder(ship, weapon, alert)){
			return;
		}

		if (!weaponManager.isLoaded(weapon))
			return;

		if (ship.shipSizeClass < 0){
			for (var i = 0; i < ship.systems.length; i++){
				for (var b = 0; i < ship.systems.systems; b++){
					if (ship.systems[i].systems[b].weapon){
						gamedata.selectedSystems.push(ship.systems[i].systems[b].weapon);
						shipWindowManager.setDataForSystem(ship, ship.systems[i].systems[b].weapon);
					}
				}
			}

		}

		gamedata.selectedSystems.push(weapon);
		shipWindowManager.setDataForSystem(ship, weapon);

	},

	isSelectedWeapon: function(weapon){
		if ($.inArray(weapon, gamedata.selectedSystems) >= 0)
			return true;

		return false;
	},

	targetingShipTooltip: function(ship, e, calledid){
		//e.find(".shipname").html(ship.name);
		var selectedShip = gamedata.getSelectedShip();
		var f = $(".targeting", e);
		f.html("");

		if (calledid == null){
			var html = "";
			var section = weaponManager.getShipHittingSide(selectedShip, ship);


			for (var i = 0; i < section.length; i++){
				if (section[i] == 1){
					html += "-FRONT-";
				}
				if (section[i] == 2){
					html += "-AFT-";
				}
				if (section[i] == 3){
					html += "-PORT-";
				}
				if (section[i] == 4){
					html += "-STARBORD-";
				}
			}
			$('<div><span class="weapon">' + html + '</span></div>').appendTo(f);
		}


		for (var i in gamedata.selectedSystems){
			var weapon = gamedata.selectedSystems[i];

			if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)){
				if(weaponManager.checkIsInRange(selectedShip, ship, weapon)){

					var value = weapon.firingMode;
						value = weapon.firingModes[value];

					if (value != "Standard"){
						$('<div><span class="weapon">'+weapon.displayName+':</span><span class="hitchange"> '+weaponManager.calculateHitChange(selectedShip, ship, weapon, calledid)+'%  (PIERCING)'  + '</span></div>').appendTo(f);
					}
					else {
						$('<div><span class="weapon">'+weapon.displayName+':</span><span class="hitchange"> '+weaponManager.calculateHitChange(selectedShip, ship, weapon, calledid)+'%</span></div>').appendTo(f);
					}
				}
				else{
					$('<div><span class="weapon">'+weapon.displayName+':</span><span class="hitchange"> NOT IN RANGE</span></div>').appendTo(f);
				}
			}
			else{
				$('<div><span class="weapon">'+weapon.displayName+':</span><span class="notInArc"> NOT IN ARC </span></div>').appendTo(f);
			}
		}
	},

	canCalledshot: function(target, system){
		var shooter = gamedata.getSelectedShip();


		if (!shooter)
			return false;

		var loc = weaponManager.getShipHittingSide(shooter, target);
	//	console.log(loc);
	//	console.log("aiming at: " + loc + " , system is on: " + system.location);
		if (target.flight)
			return false;


		if (target.shipSizeClass == 3){

			if (system.location == 0 && system.weapon){
				return true;

			}
			for (var i = 0; i < loc.length; i++) {
				if (system.location == loc[i]){
					return true
				}
			}
			if (target.draziCap && system.name == "thruster" && system.location == 2){
				for (var i = 0; i < loc.length; i++) {
					if(loc[i] == 2){
						return true;
					}
				}
			}
		}

		if (target.draziHCV){
			for (var i = 0; i < loc.length; i++) {
				if (system.location == loc[i]){
				return true
				}
			}
			if (system.location == 0 && system.weapon){
				return true
			}
			if (system.name == "thruster" && system.location == 0){
				var thruster = weaponManager.getTargetableThruster(shooter, target);
				if (system.direction == thruster){
					return true
				}
			}
		}

		if (target.shipSizeClass == 1 || target.shipSizeClass == 2 && system.name == "thruster" && system.location == 0){
			var thruster = weaponManager.getTargetableThruster(shooter, target);
			if (system.direction == thruster){
				return true
			}
		}
		if (target.shipSizeClass == 1 || target.shipSizeClass == 2){
			if (system.location == 0 && system.weapon){
				return true;
			}
			for (var i = 0; i < loc.length; i++) {
				if (system.location == loc[i]){
					return true
				}
			}
		}
		return false;
	},

	getTargetableThruster: function(shooter, target){
		var targetFacing = (shipManager.getShipHeadingAngle(target));
		var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target,shooter);

		if (target.draziHCV){
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))){
				return 1
			}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))){
				return 2
			}
		}
		if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
					return 3;
		}
		if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
					return 4;
		}
	},

	isPosOnWeaponArc: function(shooter, position, weapon){

		var shooterFacing = (shipManager.getShipHeadingAngle(shooter));
		var targetCompassHeading = mathlib.getCompassHeadingOfPosition(shooter, position);

		var arcs = shipManager.systems.getArcs(shooter, weapon);
		arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
		arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);

		//console.log("shooterFacing: " + shooterFacing + " targetCompassHeading: " +targetCompassHeading);

		return (mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end));


	},

	isOnWeaponArc: function(shooter, target, weapon){

		var shooterFacing = (shipManager.getShipHeadingAngle(shooter));
		var targetCompassHeading = mathlib.getCompassHeadingOfShip(shooter, target);

		var arcs = shipManager.systems.getArcs(shooter, weapon);
		arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
		arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);
		var oPos = shipManager.getShipPosition(shooter);
		var tPos = shipManager.getShipPosition(target);

		if (weapon.ballistic && oPos.x == tPos.x && oPos.y == tPos.y)
			return true;

		//console.log("shooterFacing: " + shooterFacing + " targetCompassHeading: " +targetCompassHeading);

		return (mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end));


	},

	calculateRangePenalty: function(distance, weapon){
		var rangePenalty = (weapon.rangePenalty*distance);

		return rangePenalty;
	},

	calculataBallisticHitChange: function(ball, calledid){
		var shooter = gamedata.getShip(ball.shooterid);
		var weapon = shipManager.systems.getSystem(shooter, ball.weaponid);
		var target = gamedata.getShip(ball.targetid);

		if(shooter.flight){
			return weaponManager.calculateFighterBallisticHitChange(shooter, target, weapon, calledid);
		}

		if (!ball.targetid)
			return false;

		var distance = (mathlib.getDistanceBetweenShipsInHex(shooter, target)).toFixed(2);

		var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);

		var defence = weaponManager.getShipDefenceValuePos(ball.position, target);
		//console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);
		var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);

		var soew = ew.getSupportedOEW(shooter, target);
		var dist = ew.getDistruptionEW(shooter);

		var oew = 0;

		if (weapon.useOEW)
		{
			oew = ew.getTargetingEW(shooter, target);
			oew -= dist;

			if (oew<0)
				oew = 0;
		}

		var firecontrol =  weaponManager.getFireControl(target, weapon);

		var intercept = weaponManager.getInterception(ball);

		var mod = 0;

		mod -= target.getHitChangeMod(shooter, ball.position);

		if (!shooter.flight && !shooter.osat)
			mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "CnC"), "PenaltyToHit");

		if (shooter.osat && shipManager.movement.hasTurned(shooter)){
			console.log("osat turn -1");
			mod -= 1;
		}

		if (calledid)
			mod -= 8;

		var ammo = weapon.getAmmo(weaponManager.getFireOrderById(ball.fireOrderId));
		if (ammo)
			mod += ammo.hitChanceMod;

		var goal = (baseDef - rangePenalty - intercept + oew + soew + firecontrol + mod);

		var change = Math.round((goal/20)*100);
	//	console.log("rangePenalty: " + rangePenalty + "intercept: " + intercept + " baseDef: " + baseDef + " oew: " + oew + " defence: " + defence + " firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);

		return change;

	},

	getInterception: function(ball){

		var intercept = 0;

		for (var i in gamedata.ships){
			var ship = gamedata.ships[i];
			var fires = weaponManager.getAllFireOrders(ship);
			for (var a in fires){
				var fire = fires[a];
				if (fire.type == "intercept" && fire.targetid == ball.fireOrderId){
					var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
					intercept += weapon.getInterceptRating();
				}
			}


		}

		return intercept;

	},

	calculateBaseHitChange: function(target, base, shooter, weapon){

  		var jink = 0;
		var dew = 0;

		if (target.flight && shooter){
			if (! shooter.flight)
			{
				jink = shipManager.movement.getJinking(target);
			} else {
				if (shooter){
					var sPosHex = shipManager.getShipPosition(shooter);
					var tPosHex = shipManager.getShipPosition(target);

					if (! (sPosHex.x == tPosHex.x && sPosHex.y == tPosHex.y)
						|| shipManager.movement.getJinking(shooter) > 0){
						jink = shipManager.movement.getJinking(target);

     			}
				}
			}
		} else {
			dew = ew.getDefensiveEW(target);
		}

		var bdew = 0;
		var sdew = 0;

		sdew = ew.getSupportedDEW(target);
		bdew = ew.getSupportedBDEW(target);

		if (shooter && shooter.flight && ! weapon.ballistic){
			dew = 0;
			bdew = 0;
			sdew = 0;
		}

		return base - dew - jink - bdew - sdew;
	},

	calculateHitChange: function(shooter, target, weapon, calledid){

		var sPos = shipManager.getShipPositionInWindowCo(shooter);
		var tPos = shipManager.getShipPositionInWindowCo(target);
		var distance = (mathlib.getDistanceBetweenShipsInHex(shooter, target)).toFixed(2);
		var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);
		var sPosHex = shipManager.getShipPosition(shooter);
		var defence = weaponManager.getShipDefenceValuePos(sPosHex, target);
		//console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);

		var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);

		var soew = ew.getSupportedOEW(shooter, target);
		var dist = ew.getDistruptionEW(shooter);

		var oew = 0;

		if (weapon.useOEW)
		{
			oew = ew.getTargetingEW(shooter, target);
			oew -= dist;

			if (oew<0)
				oew = 0;
		}

		var mod = 0;

		mod -= target.getHitChangeMod(shooter, sPos);

		if (shooter.flight){
			oew = shooter.offensivebonus;
			mod -= shipManager.movement.getJinking(shooter);

			if (shipManager.movement.hasCombatPivoted(shooter))
				mod--;
		}
		else
		{
			if (weapon.piercing && weapon.firingMode == 2 && weapon.firingModes[1] !== "Piercing"){
				mod -= 4;
			}

//			if (shipManager.movement.hasRolled(shooter)){
			if (shipManager.movement.isRolling(shooter)){
		//		console.log("is rolling -3");
				mod -= 3;
			}


			if (shipManager.movement.hasPivotedForShooting(shooter)){
		//		console.log("pivoting");
				mod -= 3;
			}

			if (shooter.osat && shipManager.movement.hasTurned(shooter)){
		//		console.log("osat turn -1");
				mod -= 1;
			}

			if (!shooter.osat){
				mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "CnC"), "PenaltyToHit");
			}
		}

		if (calledid){
			mod += weapon.calledShotMod;
		}

		var ammo = weapon.getAmmo(null);
		if (ammo)
			mod += ammo.hitChanceMod;

		var jammermod = 0;

//		if (target.flight && distance > 10){
//			oew = 0;
//		}

		if (oew < 1){
			rangePenalty = rangePenalty*2;
		 }else if (shooter.faction != target.faction){
			var jammer = shipManager.systems.getSystemByName(target, "jammer");
			var stealth = shipManager.systems.getSystemByName(target, "stealth");

			if (jammer && !shipManager.power.isOffline(target, jammer))
				jammermod = rangePenalty*shipManager.systems.getOutput(target, jammer);

			if(stealth && (mathlib.getDistanceHex(sPos, tPos) > 5))
				jammermod = rangePenalty;

			if (target.flight){
				var jinking = shipManager.movement.getJinking(target);
				if ( jinking > jammermod){
					jammermod = 0;
				}
				else{
					jammermod = jammermod - jinking;
				}
			}
		}

		var firecontrol =  weaponManager.getFireControl(target, weapon);

		var goal = (baseDef - jammermod - rangePenalty + oew + soew + firecontrol + mod);

		var change = Math.round((goal/20)*100);
	//	console.log("rangePenalty: " + rangePenalty + "jammermod: "+jammermod+" baseDef: " + baseDef + " oew: " + oew + " soew: "+soew+" firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);

	// makes no sense to cap hit % visuals at 100. You wanne calc your estimated pulses, right ?
	//    if (change > 100)
	//       change = 100;
		return change;


	},

	calculateFighterBallisticHitChange: function(shooter, target, weapon, calledid){

		var sPos = shipManager.getShipPositionInWindowCo(shooter);
		var tPos = shipManager.getShipPositionInWindowCo(target);
		var distance = (mathlib.getDistanceBetweenShipsInHex(shooter, target)).toFixed(2);
		var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);
		var sPosHex = shipManager.getShipPosition(shooter);
		var tPosHex = shipManager.getShipPosition(target);
		var defence = weaponManager.getShipDefenceValuePos(sPosHex, target);
		//console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);
		var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);

		var soew = ew.getSupportedOEW(shooter, target);
		var dist = ew.getDistruptionEW(shooter);

		var oew = 0;

		if (weapon.useOEW)
		{
			oew = ew.getTargetingEW(shooter, target);
			oew -= dist;

			if (oew<0)
				oew = 0;
		}

		var mod = 0;

		mod -= target.getHitChangeMod(shooter, sPos);

		if(shooter.hasNavigator || weaponManager.isPosOnWeaponArc(shooter, tPosHex, weapon)){
			oew = shooter.offensivebonus;
		}

		mod -= shipManager.movement.getJinking(shooter);

		if (shipManager.movement.hasCombatPivoted(shooter)){
			mod--;
		}

		if (calledid){
			mod += weapon.calledShotMod;
		}

		var ammo = weapon.getAmmo(null);
		if (ammo){
			mod += ammo.hitChanceMod;
		}

		var jammermod = 0;
		if (shooter.faction != target.faction){
			var jammer = shipManager.systems.getSystemByName(target, "jammer");
			var stealth = shipManager.systems.getSystemByName(target, "stealth");

			if (jammer && !shipManager.power.isOffline(target, jammer))
				jammermod = rangePenalty*shipManager.systems.getOutput(target, jammer);

			if(stealth && (mathlib.getDistanceHex(sPos, tPos) > 5))
				jammermod = rangePenalty;

			if (target.flight){
				var jinking = shipManager.movement.getJinking(target);
				if ( jinking > jammermod){
					jammermod = 0;
				}
				else{
					jammermod = jammermod - jinking;
				}
			}
		}

		var firecontrol =  weaponManager.getFireControl(target, weapon);

		var goal = (baseDef - jammermod - rangePenalty + oew + soew + firecontrol + mod);

		var change = Math.round((goal/20)*100);
		console.log("rangePenalty: " + rangePenalty + "jammermod: "+jammermod+" baseDef: " + baseDef + " oew: " + oew + " soew: "+soew+" firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);

		if (change > 100)
			change = 100;
		return change;


	},

	getFireControl: function(target, weapon){

		if (target.shipSizeClass > 1){
			return weapon.fireControl[2];
		}
		if (target.shipSizeClass >= 0){
			return weapon.fireControl[1];
		}

		return weapon.fireControl[0];


	},

	// 'position' should be in HEX coordinate
	getShipDefenceValuePos: function(position, target){
		var targetFacing = (shipManager.getShipHeadingAngle(target));
		var targetPos = shipManager.getShipPosition(target);

		var shooterCompassHeading = mathlib.getCompassHeadingOfPosition(target, position);



		//console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);

		//console.log("ship degree: " +delta);
		if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing) )){
			//console.log("hitting front 1");
			return target.forwardDefense;
		}else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing) )){
			//console.log("hitting rear 2");
			return target.forwardDefense;
		}else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing) )){
			//console.log("hitting port 3");
			return target.sideDefense;
		}else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing) )){
			//console.log("hitting starboard 4");
			return target.sideDefense;
		}

		return target.sideDefense;

	},

	getShipHittingSide: function(shooter, target){
		var targetFacing = (shipManager.getShipHeadingAngle(target));
		var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target,shooter);

	//    console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);

		 if (target.draziCap){
 			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))){
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
					return [1, 3]
				}
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
					return [1, 4]
				}
				return [1];
			}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))){
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
					return [2, 3]
				}
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
					return [2, 4]
				}
				return [2];
			}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
				return [3];
			}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
				return [4];
			}

			else return [0]
		}

		if (target.draziHCV){
			if 	(mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(180, targetFacing), mathlib.addToDirection(360, targetFacing)) &&
				(mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(0, targetFacing), mathlib.addToDirection(180, targetFacing)))){
				return [3, 4];
				}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(180, targetFacing), mathlib.addToDirection(360, targetFacing))){
				return [3];
			}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(0, targetFacing), mathlib.addToDirection(180, targetFacing))){
				return [4];
			}
		}

		 if (target.shipSizeClass > 0 && target.shipSizeClass < 3){
			if  (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(90, targetFacing), mathlib.addToDirection(270, targetFacing)) &&
				(mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(270, targetFacing), mathlib.addToDirection(90, targetFacing)))){
				return [1, 2];
			}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(270, targetFacing), mathlib.addToDirection(90, targetFacing))){
				return [1];
			}
			if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(90, targetFacing), mathlib.addToDirection(270, targetFacing))){
				return [2];
			}
		 }

		 if (target.shipSizeClass == 3){
 			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))){
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
					return [1, 3]
				}
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
					return [1, 4]
				}
				return [1];
			}
			if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))){
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
					return [2, 3]
				}
				if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
					return [2, 4]
				}
				return [2];
			}
			if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
				return [3];
			}
			if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
				return [4];
			}
		}
		return 0;
	},

	getShipDefenceValue: function(shooter, target){
		var targetFacing = (shipManager.getShipHeadingAngle(target));
		var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target,shooter);



		//console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);

		//console.log("ship degree: " +delta);
		if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing) )){
			//console.log("hitting front 1");
			return target.forwardDefense;
		}else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing) )){
			//console.log("hitting rear 2");
			return target.forwardDefense;
		}else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing) )){
			//console.log("hitting port 3");
			return target.sideDefense;
		}else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing) )){
			//console.log("hitting starboard 4");
			return target.sideDefense;
		}

		return target.sideDefense;

	},
	/*
	canIntercept: function(ball){
		var selectedShip = gamedata.getSelectedShip();
		if (shipManager.isDestroyed(selectedShip))
			return false;

		if (!ball.targetid || ball.targetid != selectedShip.id)
			return false;

		for (var i in gamedata.selectedSystems){
			var weapon = gamedata.selectedSystems[i];

			if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
				continue;

			if (weaponManager.isPosOnWeaponArc(selectedShip, ball.position, weapon)){
				return true;
			}
		}

		return false;
	},
	*/
	targetBallistic: function(ball){
		 if (gamedata.gamephase != 3)
			return;


		var selectedShip = gamedata.getSelectedShip();
		if (shipManager.isDestroyed(selectedShip))
			return;

		if (!ball.targetid)
			return;

		var target = gamedata.getShip(ball.targetid);

		var toUnselect = Array();

		for (var i in gamedata.selectedSystems){
			var weapon = gamedata.selectedSystems[i];

			if (ball.targetid != selectedShip.id
				&& ! weapon.freeintercept
				&& ! shipManager.isEscorting(selectedShip, target))
				continue;

			if (ball.targetid != selectedShip.id && weapon.freeintercept){
				var ballpos = hexgrid.positionToPixel(ball.position);
				var targetpos = shipManager.getShipPositionInWindowCo(target);
				var selectedpos = shipManager.getShipPositionInWindowCo(selectedShip);
				if (mathlib.getDistanceHex(ballpos, targetpos) <= mathlib.getDistanceHex(ballpos, selectedpos) || mathlib.getDistanceHex(targetpos, selectedpos) >3)
					continue;
			}
			if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
				continue;
			if (weapon.getInterceptRating() == 0)
				continue;

			var type = 'intercept';


			if (weaponManager.isPosOnWeaponArc(selectedShip, ball.position, weapon)){
				weaponManager.removeFiringOrder(selectedShip, weapon);
				for (var s=0;s<weapon.guns;s++){
					weapon.fireOrders.push({id:null,type:type, shooterid:selectedShip.id, targetid:ball.fireOrderId, weaponid:weapon.id, calledid:-1, turn:gamedata.turn, firingMode:weapon.firingMode, shots:weapon.defaultShots, x:"null", y:"null"});
				}
				toUnselect.push(weapon);
			}
		}

		for (var i in toUnselect){
			weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
		}

		gamedata.shipStatusChanged(selectedShip);

	},
	


	canSelfIntercept: function(ship){
		for (var i in gamedata.selectedSystems){
			var weapon = gamedata.selectedSystems[i];
			
			if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && weapon.loadingtime > 1){
				return true
			}
		}
		return false;
	},




	checkSelfIntercept: function(ship){

		var invalid = [];
		var valid = [];

		for (var i in gamedata.selectedSystems){
			var weapon = gamedata.selectedSystems[i];

				if(weaponManager.hasFiringOrder(ship, weapon)){
					weaponManager.removeFiringOrder(ship, weapon);
				}
				
				if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && weapon.loadingtime > 1){
					valid.push(weapon);
				} else invalid.push(weapon);
		}


		if (valid.length > 0){
			weaponManager.confirmSelfIntercept(ship, valid, invalid, "Do you want to order the selected weapons to intercept incoming fire ?");
		}
	},



    confirmSelfIntercept: function(ship, valid, invalid, message){
        confirm.confirmWithOptions(message, "Yessss", "Nope", function(response){
        	if (response){
        		weaponManager.setSelfIntercept(ship, valid);
	        	for (var i in invalid){
					weaponManager.unSelectWeapon(ship, invalid[i]);
				}
			}
        });
    },





	setSelfIntercept: function(ship, valid){

		for (var weapon in valid){
			var weapon = valid[weapon];

			var fireid = ship.id+"_"+weapon.id +"_"+(weapon.fireOrders.length+1);

			var fire = {
				id:fireid,
				type:"selfIntercept",
				shooterid:ship.id,
				targetid:ship.id,
				weaponid:weapon.id,
				calledid:-1,
				turn:gamedata.turn,
				firingMode:weapon.firingMode,
				shots:weapon.defaultShots,
				x:"null",
				y:"null",
				addToDB: true
			};

			weapon.fireOrders.push(fire);
			weaponManager.unSelectWeapon(ship, weapon);
		}
	//	gamedata.shipStatusChanged(ship);

	},

	//system is for called shot!
	targetShip: function(ship, system){

		var selectedShip = gamedata.getSelectedShip();
		if (shipManager.isDestroyed(selectedShip))
			return;

		var toUnselect = Array();
		for (var i in gamedata.selectedSystems){
			var weapon = gamedata.selectedSystems[i];

			if(weapon.targetImmobile){
				confirm.error("A " + weapon.displayName + " can only target immobile enormous \
							   units,<br> bases, or planets.<br>(Launching \
							   ship must be speed zero.)");
				toUnselect.push(weapon);
				continue;
			}

			if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
				continue;


			if (weapon.ballistic && gamedata.gamephase != 1){
				continue;
			}
			if (!weapon.ballistic && gamedata.gamephase != 3){
				continue;
			}

			if (weapon.ballistic && system)
				continue;

			if (weaponManager.checkConflictingFireOrder(selectedShip, weapon, true)){
				for(var j = gamedata.selectedSystems.length - 1; j>= 0; j--){
					var sel_weapon = gamedata.selectedSystems[j];

					weaponManager.removeFiringOrder(selectedShip, sel_weapon);
					weaponManager.unSelectWeapon(selectedShip, sel_weapon);
				}

				return;
			}

			if (ship.flight && weapon.fireControl[0] === null)
				continue;

			if (!ship.flight && ship.shipSizeClass < 2 && weapon.fireControl[1] === null)
				continue;

			if (ship.shipSizeClass >= 2 && weapon.fireControl[2] === null)
				continue;

			var type = 'normal';
			if (weapon.ballistic){
				type = 'ballistic';
			}

			if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)){
				if (weaponManager.checkIsInRange(selectedShip, ship, weapon)){
					weaponManager.removeFiringOrder(selectedShip, weapon);
					for (var s=0;s<weapon.guns;s++){
						var fireid = selectedShip.id+"_"+weapon.id +"_"+(weapon.fireOrders.length+1);
						var calledid = -1;

						if (system){
							// When the system is a subsystem, make all damage go through
							// the parent.
							while(system.parentId > 0){
								system = shipManager.systems.getSystem(ship, system.parentId);
							}

							calledid = system.id;
						}

						var fire = {
							id:fireid,
							type:type,
							shooterid:selectedShip.id,
							targetid:ship.id,
							weaponid:weapon.id,
							calledid:calledid,
							turn:gamedata.turn,
							firingMode:weapon.firingMode,
							shots:weapon.defaultShots,
							x:"null",
							y:"null"
						};
						weapon.fireOrders.push(fire);

					}
					if (weapon.ballistic){
						gamedata.ballistics.push({id:(gamedata.ballistics.length), fireid:fireid, position:shipManager.getShipPosition(selectedShip),
						facing:shipManager.movement.getLastCommitedMove(selectedShip).facing,
						targetposition:{x:null, y:null},
						targetid:ship.id,
						shooterid:selectedShip.id,
						weaponid:weapon.id,
						shots:fire.shots});

						ballistics.calculateBallisticLocations();
						ballistics.calculateDrawBallistics();
						drawEntities();
					}
					toUnselect.push(weapon);
				}
			}
		}

		for (var i in toUnselect){
			weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
		}

		gamedata.shipStatusChanged(selectedShip);

	},

	checkIsInRange: function(shooter, target, weapon){

		var range = weapon.range;
		var shooterPos = shipManager.getShipPositionInWindowCoWithoutOffset(shooter);
		var targetPos = shipManager.getShipPositionInWindowCoWithoutOffset(target)
		var distance = (mathlib.getDistanceBetweenShipsInHex(shooter, target)).toFixed(2);

		var stealthSystem = shipManager.systems.getSystemByName(target, "stealth");

		if(stealthSystem && distance > 5 && weapon.ballistic){
			return false;
		}

		if (range === 0)
			return true;

		var jammer = shipManager.systems.getSystemByName(target, "jammer");

		if (jammer)
		{
			range = range / (shipManager.systems.getOutput(target, jammer)+1);
		}

		return (distance <= range);
	},

	targetHex: function(hexpos){

		var selectedShip = gamedata.getSelectedShip();
		if (shipManager.isDestroyed(selectedShip))
			return;



		var toUnselect = Array();
		for (var i in gamedata.selectedSystems){
			var weapon = gamedata.selectedSystems[i];

			if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
				continue;


			if (weapon.ballistic && gamedata.gamephase != 1){
				continue;
			}
			if (!weapon.ballistic && gamedata.gamephase != 3){
				continue;
			}

			if (!weapon.hextarget)
				continue;

			if (weaponManager.checkConflictingFireOrder(selectedShip, weapon)){
				continue;
			}

			var type = 'normal';
			if (weapon.ballistic){
				type = 'ballistic';
			}


			if (weaponManager.isPosOnWeaponArc(selectedShip, hexpos, weapon)){
				if (weapon.range == 0 || (mathlib.getDistanceHex(shipManager.getShipPositionInWindowCo(selectedShip), hexgrid.positionToPixel(hexpos))<=weapon.range)){
					weaponManager.removeFiringOrder(selectedShip, weapon);
					for (var s=0;s<weapon.guns;s++){

						var fireid = selectedShip.id+"_"+weapon.id +"_"+(weapon.fireOrders.length+1);
						var fire = {id:fireid,type:type, shooterid:selectedShip.id, targetid:-1, weaponid:weapon.id, calledid:-1, turn:gamedata.turn, firingMode:weapon.firingMode, shots:weapon.defaultShots, x:hexpos.x, y:hexpos.y};
						weapon.fireOrders.push(fire);

					}
					if (weapon.ballistic){
						gamedata.ballistics.push({id:(gamedata.ballistics.length), fireid:fireid, position:shipManager.getShipPosition(selectedShip),
						facing:shipManager.movement.getLastCommitedMove(selectedShip).facing,
						targetposition:hexpos,
						targetid:-1,
						shooterid:selectedShip.id,
						weaponid:weapon.id,
						shots:fire.shots});

						ballistics.calculateBallisticLocations();
						ballistics.calculateDrawBallistics();
						drawEntities();
					}

					toUnselect.push(weapon);
				}
			}
		}

		for (var i in toUnselect){
			weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
		}

		gamedata.shipStatusChanged(selectedShip);

	},


	removeFiringOrder: function(ship, system){

		for(var i = system.fireOrders.length-1; i >= 0; i--){
			if(system.fireOrders[i].weaponid == system.id){

				for(var a = gamedata.ballistics.length-1; a >= 0; a--){
					if (gamedata.ballistics[a].fireid == system.fireOrders[i].id && gamedata.ballistics[a].shooterid == ship.id){
						var id = gamedata.ballistics[a].id;

						$('#ballistic_launch_canvas_'+id).remove();
						$('#ballistic_target_canvas_'+id).remove();
						$('.ballistic_'+id).remove();
						gamedata.ballistics.splice(a,1);
					}
				}
				system.fireOrders.splice(i,1);
			}
		}
		ballistics.calculateBallisticLocations();
		ballistics.calculateDrawBallistics();
		drawEntities();


	},



	hasFiringOrder: function(ship, system){

		for (var i in system.fireOrders){
			var fire = system.fireOrders[i];
			if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled){
				if (((gamedata.gamephase == 1 || gamedata.gamephase == 3 ) && system.ballistic) || (gamedata.gamephase == 3 && !system.ballistic)){
					if (fire.type == "selfIntercept"){
						return "self";
					} else return true;
				}
			}
		}

		if(system.duoWeapon){
			for(var i in system.weapons){
				if(weaponManager.hasFiringOrder(ship, system.weapons[i])){
					return true;
				}
			}
		}

		return false;

	},

	shipHasFiringOrder: function(ship)
	{
	  //TODO:implement
	},

	canCombatTurn: function(ship){

		var fires = weaponManager.getAllFireOrders(ship);
		for (var i in fires){
			var fire = fires[i];
			var weapon = shipManager.systems.getSystem(ship, fire.weaponid)
			if (fire.turn == gamedata.turn && !fire.rolled && !weapon.ballistic){
				return false;
			}

		}
		return true;

	},

	getFiringOrder: function(ship, system){

		var fires = weaponManager.getAllFireOrders(ship);
		for (var i in fires){
			var fire = fires[i];
			if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled)
				return fire;
		}

		return false;

	},

	getAllFireOrders: function(ship)
	{
		var fires = new Array();
		for (var i in ship.systems)
		{
			if (ship.flight){
				var fighter = ship.systems[i];
				for (var a in fighter.systems){
					var system = fighter.systems[a];
					var sysFires = weaponManager.getAllFireOrdersFromSystem(system);
					if (sysFires)
						fires = fires.concat(sysFires);
				}

			}else{
				var system = ship.systems[i];
				var sysFires = weaponManager.getAllFireOrdersFromSystem(system);
				if (sysFires)
					fires = fires.concat(sysFires);
			}

		}
		return fires;
	},

	getAllFireOrdersFromSystem: function(system){
		if (! system.weapon)
			return;

		var fires = system.fireOrders;

		if (system.dualWeapon || system.duoWeapon){
			for (var i in system.weapons){
				var weapon = system.weapons[i];

				if(weapon.duoWeapon){
					for(var index in weapon.weapons){
						var subweapon = weapon.weapons[index];
						fires = fires.concat(weaponManager.getAllFireOrdersFromSystem(subweapon));
					}
				}else{
					fires = fires.concat(weaponManager.getAllFireOrdersFromSystem(weapon));
				}
			}
		}

		return fires;

	},

	getInterceptingFiringOrders: function(id){
		var intercepts = Array();

		for (var a in gamedata.ships){
			var ship = gamedata.ships[a];
			var fires = weaponManager.getAllFireOrders(ship);
			for (var i in fires){
				var fire = fires[i];
				if (fire.targetid == id && fire.turn == gamedata.turn && fire.type == "intercept" ||
					fire.type == "selfIntercept" && fire.targetid == id && fire.turn == gamedata.turn){
					intercepts.push(fire);
				}
			}
		}

		return intercepts;

	},

	changeShots: function(ship, system, mod){
		var fires = weaponManager.getAllFireOrders(ship);
		for (var i in fires){
			var fire = fires[i];
			if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled){
				if ((gamedata.gamephase == 1 && system.ballistic) || (gamedata.gamephase == 3 && !system.ballistic))
					fire.shots += mod;
			}

		}

		shipWindowManager.setDataForSystem(ship, system);
	},

	getDamagesCausedBy: function(damages, fire){

		for (var i in gamedata.ships){
			var ship = gamedata.ships[i];
			var list = Array();

			for (var a in ship.systems){
				var system = ship.systems[a];
				for (var b in system.damage){
					var d = system.damage[b];
					if (d.fireorderid == fire.id){
						list.push(d);
					}

				}

			}

			if (list.length>0){
				//console.log(list);
				var found = false;
				for (var a in damages){
					var entry = damages[a];
					if (entry.ship.id == ship.id){
						found = true;
						entry.damages = entry.damages.concat(list);
					}
				}
				if (!found)
					damages.push({ship:ship, damages:list});
			}

		}

		return damages;



	},

	removeArcIndicators: function(){

		for(var i = EWindicators.indicators.length-1; i >= 0; i--){
			if(EWindicators.indicators[i].type == "Arcs"){
				EWindicators.indicators.splice(i,1);
			}
		}


	},

	addArcIndicators: function(ship, weapon){
		weapon = shipManager.systems.initializeSystem(weapon);

		weaponManager.removeArcIndicators(ship);
		var ind = weaponManager.makeWeaponArcindicator(ship, weapon);

		if (ind)
			EWindicators.indicators.push(ind);

	},

	makeWeaponArcindicator: function(ship, weapon){
		var effect = {};

		var a = shipManager.getShipHeadingAngle(ship);
		var arcs = shipManager.systems.getArcs(ship, weapon);
		var dis;
		if (weapon.rangePenalty == 0){
			dis =  hexgrid.hexWidth()*weapon.range;
		}else{
			dis =  20*hexgrid.hexWidth()/weapon.rangePenalty;
		}

		arcs.start = mathlib.addToDirection(arcs.start, a);
		arcs.end = mathlib.addToDirection(arcs.end, a);
		//console.log("start: " + arcs.start + " end: " +arcs.end);
		effect.ship = ship;
		effect.type = "Arcs"
		effect.arcs = arcs;
		effect.dis = dis;
		effect.draw = function(self){
			var arcs = self.arcs;
			var canvas = EWindicators.getEwCanvas();



			var pos = shipManager.getShipPositionForDrawing(self.ship);
			canvas.strokeStyle = "rgba(20,80,128,0.2)";
			canvas.fillStyle = "rgba(20,80,128,0.2)";
			if (arcs.start == arcs.end){
				graphics.drawCircleAndFill(canvas, pos.x, pos.y, self.dis, 1);
			}else{


				var p1 = mathlib.getPointInDirection(self.dis, arcs.start, pos.x, pos.y);
				var p2 = mathlib.getPointInDirection(self.dis, arcs.end, pos.x, pos.y);


				graphics.drawCone(canvas, pos, p1, p2, arcs, 1)
			}

		};

		return effect;

	},


	isLoaded: function(weapon){
		return (weapon.loadingtime <= weapon.turnsloaded
			|| weapon.loadingtime <= weapon.overloadturns );
	},

	getFireOrderById: function(id){

		for (var i in gamedata.ships){
			for (var a in gamedata.ships[i].fireOrders){
				var fire = gamedata.ships[i].fireOrders[a];
				if (fire.id == id)
					return fire;
			}


		}

		return false;

	},

	getFiringWeapon: function(weapon, fire){

		return weapon;
	},



    canRam: function(ship){
        if (ship.hasOwnProperty("hunterkiller")){

        }
    },


    askForRam: function(target){

		var selectedShip = gamedata.getSelectedShip();


        confirm.confirmWithOptions("CONFIRM movement ?", "Yup", "Nah, too risky yo", function(respons){
            if(respons){
                console.log("ye");
            }
            else{
                console.log("na");
            }
        });
    }






}





