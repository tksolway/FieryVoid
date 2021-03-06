<?php
class Strela extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 510;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Strela";
        $this->imagePath = "img/ships/strela.png";
        $this->shipClass = "Strela Light Jumpship";
        $this->fighters = array("medium"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 7));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));

        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 240, 0));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 0, 120));
        
        $this->addAftSystem(new AssaultLaser(4, 6, 4, 120, 240));
        $this->addAftSystem(new Thruster(4, 16, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 5, 2));
        $this->addAftSystem(new JumpEngine(3, 15, 3, 20));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 240));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 5, 40 ));
        
        
    }

}



?>
