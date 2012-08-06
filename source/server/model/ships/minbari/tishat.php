<?php
class Tishat extends FighterFlight{

    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);

		$this->pointCost = 510;
		$this->faction = "Minbari";
		$this->phpclass = "Tishat";
		$this->shipClass = "Tishat flight";
		// need picture
		$this->imagePath = "img/ships/tishat.png";

        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 14;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        $this->iniativebonus = 95;
        $this->gravitic = true;

        for ($i = 0; $i<6; $i++){
            $armour = array(3, 3, 3, 3);
            $tishat = new Fighter($armour, 9, $this->id);
            $tishat->displayName = "Tishat Medium Fighter";
        // need picture
            $tishat->imagePath = "img/ships/tishat.png";
        // need picture
            $tishat->iconPath = "img/ships/tishat-large.png";
            $tishat->addFrontSystem(new LightFusionCannon(330, 30, 4, 2));
            $nial->addAftSystem(new Jammer(0, 1, 0));
            $this->addSystem($tishat);
        }
    }
}
?>
