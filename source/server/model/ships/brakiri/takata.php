<?php
class Takata extends BaseShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 950;
		$this->faction = "Brakiri";
		$this->phpclass = "Takata";
		$this->imagePath = "img/ships/Takata.png";
		$this->shipClass = "Takata Mine Cruiser";
		$this->shipSizeClass = 3;

		$this->forwardDefense = 15;
		$this->sideDefense = 17;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.5;
		$this->accelcost = 4;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 10;

		$this->gravitic = true;

		$this->addPrimarySystem(new Reactor(6, 27, 0, 6));
		$this->addPrimarySystem(new CnC(8, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 16, 9, 10));
		$this->addPrimarySystem(new Engine(6, 18, 0, 16, 2));
		$this->addPrimarySystem(new JumpEngine(6, 20, 6, 16));
		$this->addPrimarySystem(new Hangar(6, 2));
		$this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 4));
		 
		$this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 240, 0));
		$this->addFrontSystem(new GravitonPulsar(3, 5, 2, 240, 60));
		$this->addFrontSystem(new GraviticThruster(5, 10, 0, 6, 1));
		$this->addFrontSystem(new GraviticMine(5, 6, 6, 300, 60));
		$this->addFrontSystem(new GraviticMine(5, 6, 6, 300, 60));
		$this->addFrontSystem(new GraviticMine(5, 6, 6, 300, 120));
		$this->addFrontSystem(new GraviticThruster(5, 10, 0, 6, 1));
		$this->addFrontSystem(new GravitonPulsar(3, 5, 2, 300, 120));
		$this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 0, 120));
		
		$this->addAftSystem(new GraviticShield(0, 6, 0, 3, 180, 240));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));
		$this->addAftSystem(new GravitonPulsar(3, 5, 2, 120, 300));
		$this->addAftSystem(new GravitonPulsar(3, 5, 2, 90, 180));
		$this->addAftSystem(new GravitonPulsar(3, 5, 2, 60, 240));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));
		$this->addAftSystem(new GraviticShield(0, 6, 0, 3, 0, 120));
		
		$this->addLeftSystem(new GraviticMine(5, 6, 6, 240, 0));
		$this->addLeftSystem(new GraviticMine(5, 6, 6, 240, 0));
		$this->addLeftSystem(new GraviticThruster(5, 13, 0, 8, 3));

		$this->addRightSystem(new GraviticMine(5, 6, 6, 0, 120));
		$this->addRightSystem(new GraviticMine(5, 6, 6, 0, 120));
		$this->addRightSystem(new GraviticThruster(5, 13, 0, 8, 4));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(5, 46));
		$this->addAftSystem(new Structure(5, 42));
		$this->addLeftSystem(new Structure(5, 48));
		$this->addRightSystem(new Structure(5, 48));
		$this->addPrimarySystem(new Structure(6, 40));
	}
}
?>
