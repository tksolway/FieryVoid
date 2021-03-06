<?php
class JashakarP extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 425;
	$this->faction = "Dilgar";
        $this->phpclass = "JashakarP";
        $this->imagePath = "img/ships/jashakar.png";
        $this->shipClass = "Jashakar-P Pulse Frigate";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->occurence = "uncommon";
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = 65;

        $this->addPrimarySystem(new ScatterPulsar(1, 4, 2, 240, 60));
        $this->addPrimarySystem(new ScatterPulsar(1, 4, 2, 300, 120));
        $this->addPrimarySystem(new Reactor(4, 15, 0, 4));
        $this->addPrimarySystem(new CnC(4, 11, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 7));
        $this->addPrimarySystem(new Engine(4, 9, 0, 4, 2));
	$this->addPrimarySystem(new Hangar(2, 1));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));
		
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 240, 360));
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 300, 60));
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 300, 60));
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 0, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Engine(2, 5, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new LightBolter(1, 6, 2, 240, 360));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 120, 300));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 60, 240));
        $this->addAftSystem(new LightBolter(1, 6, 2, 0, 120));
		
        $this->addPrimarySystem(new Structure( 4, 48));
    }
}
?>
