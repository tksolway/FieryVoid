<?php
class Eyehawk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "Drazi";
        $this->phpclass = "Eyehawk";
        $this->imagePath = "img/ships/sunhawk.png";
        $this->shipClass = "Eyehawk Scout Cruiser";
        $this->occurence = "rare";

        $this->forwardDefense = 14;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 14, 0, 2));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 13, 4, 8));
        $this->addPrimarySystem(new ElintScanner(4, 6, 2, 4));
        $this->addPrimarySystem(new Engine(5, 11, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new JumpEngine(4, 16, 4, 36));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 1));
        $this->addPrimarySystem(new Thruster(5, 21, 0, 10, 2));

        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(4, 4, 1, 240, 60));

        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightSystem(new StdParticleBeam(4, 4, 1, 300, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(6, 36));
        $this->addLeftSystem(new Structure(5, 44));
        $this->addRightSystem(new Structure(5, 44));
    }
}