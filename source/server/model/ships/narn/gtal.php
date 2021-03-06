<?php
class Gtal extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
	$this->faction = "Narn";
        $this->phpclass = "Gtal";
        $this->imagePath = "img/ships/gquan.png";
        $this->shipClass = "G'Tal Command Cruiser";
        $this->occurence = "rare";
        $this->fighters = array("normal"=>12);

        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        
        $this->addPrimarySystem(new Reactor(6, 25, 0, 2));
        $this->addPrimarySystem(new CnC(7, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 27, 5, 10));
        $this->addPrimarySystem(new Engine(6, 20, 0, 12, 3));
        $this->addPrimarySystem(new JumpEngine(6, 30, 3, 20));
        $this->addPrimarySystem(new Hangar(6, 16));
        
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
	$this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));

        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));	
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
	$this->addAftSystem(new HeavyLaser(4, 8, 6, 120, 240));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));

        $this->addLeftSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 270, 90));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addRightSystem(new TwinArray(3, 6, 2, 270, 90));	
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        $this->addFrontSystem(new Structure(5, 70));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 70));
        $this->addRightSystem(new Structure(4, 70));
        $this->addPrimarySystem(new Structure(6, 50));
    }
}
?>