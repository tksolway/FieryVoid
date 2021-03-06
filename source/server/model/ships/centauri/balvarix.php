<?php

class Balvarix extends BaseShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
	$this->pointCost = 650;
	$this->faction = "Centauri";
        $this->phpclass = "Balvarix";
        $this->shipClass = "Balvarix";
        $this->imagePath = "img/ships/balvarix.png";
        $this->shipSizeClass = 3;
        $this->fighters = array("medium"=>24,
            "medium (rutarian capable)"=>12);
        $this->occurence = "rare";

        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(7, 17, 0, 0));
        $this->addPrimarySystem(new CnC(7, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 16, 5, 8));
        $this->addPrimarySystem(new Engine(6, 23, 0, 10, 3));
	$this->addPrimarySystem(new Hangar(7, 14));
	$this->addPrimarySystem(new JumpEngine(7, 18, 3, 16));

	$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
	$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
        $this->addFrontSystem(new Thruster(6, 8, 0, 2, 1));
       
        $this->addAftSystem(new Thruster(5, 15, 0, 5, 2));
        $this->addAftSystem(new Thruster(5, 15, 0, 5, 2));
        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
	
	$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
	$this->addLeftSystem(new Thruster(5, 8, 0, 3, 1));
	$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
	$this->addLeftSystem(new Mattercannon(4, 7, 4, 180, 0));
        $this->addLeftSystem(new Hangar(5, 12));
		
	$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
	$this->addRightSystem(new Thruster(5, 8, 0, 3, 1));
	$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
	$this->addRightSystem(new Mattercannon(4, 7, 4, 0, 180));
        $this->addRightSystem(new Hangar(5, 12));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 40));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 7, 39));
    }
}
?>
