    <?php

class MissileLauncher extends Weapon
{
    public $useOEW = false;
    public $ballistic = true;
    public $trailColor = array(141, 240, 255);
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);
    public $animationExplosionScale = 0.25;
    public $projectilespeed = 8;
    public $animationWidth = 4;
    public $trailLength = 100;
    public $distanceRange = 0;
    
    public $firingModes = array(
    );
    
    public $missileArray = array();
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function setSystemDataWindow($turn)
    {
        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";

        parent::setSystemDataWindow($turn);
    }
    
    public function isInDistanceRange($shooter, $target, $fireOrder)
    {
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
    
        if(mathlib::getDistanceHex($pos,  $target->getCoPos()) > $this->distanceRange)
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        
        return true;
    }
    
    public function setAmmo($firingMode, $amount){
        $this->missileArray[$firingMode]->amount = $amount;
    }
}

class SMissileRack extends MissileLauncher
{
    public $name = "sMissileRack";
    public $displayName = "Class-S Missile Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 2;
    public $iconPath = "missile1.png";

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    protected function getAmmo($fireOrder)
    {
        return new $this->firingModes[$fireOrder->firingMode];
    }
    
    public function getDamage($fireOrder)
    {
        $ammo = new $this->firingModes[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;}     
}


class LMissileRack extends MissileLauncher
{
    public $name = "lMissileRack";
    public $displayName = "Class-L Missile Rack";
    public $range = 30;
    public $distanceRange = 70;
    public $loadingtime = 2;
    public $iconPath = "missile1.png";

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    protected function getAmmo($fireOrder)
    {
        return new $this->firingModes[$fireOrder->firingMode];
    }
    
    public function getDamage($fireOrder)
    {
        $ammo = new $this->firingModes[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;}     
}

class LHMissileRack extends MissileLauncher
{
    public $name = "lHMissileRack";
    public $displayName = "Class-LH Missile Rack";
    public $range = 30;
    public $distanceRange = 70;
    public $loadingtime = 1;
    public $iconPath = "missile2.png";
    
    public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals 
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    protected function getAmmo($fireOrder)
    {
        return new $this->firingModes[$fireOrder->firingMode];
    }
    
    public function getDamage($fireOrder)
    {
        $ammo = new $this->firingModes[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;} 
}

class FighterMissileRack extends MissileLauncher
{
    public $name = "FighterMissileRack";
    public $displayName = "Fighter Missile Rack";
    public $loadingtime = 1;
    public $iconPath = "fighterMissile.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;

    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "FB"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        $this->missileArray = array(
            1 => new MissileFB($startArc, $endArc)
        );
        
        $this->maxAmount = $maxAmount;

        parent::__construct(0, 0, 0, $startArc, $endArc);
    }
    
    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);

        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = $this->missileArray[$this->firingMode]->displayName;
        $this->data["Damage"] = $this->missileArray[$this->firingMode]->damage;
        $this->data["Range"] = $this->missileArray[$this->firingMode]->range;
    }

    public function calculateHit($gamedata, $fireOrder){
        $ammo = $this->missileArray[$fireOrder->firingMode];
        $ammo->calculateHit($gamedata, $fireOrder);
    }
    
    public function setId($id){
        
        debug::log("Set ID");
        parent::setId($id);
        
        $counter = 0;
        
        foreach ($this->missileArray as $missile){
            debug::log("Set ID missile, $counter");
            $missile->setId(1000 + ($id*10) + $counter);
            $counter++;
        } 
    }

    
    public function setFireControl($fighterOffensiveBonus){
        $this->fireControl[0] = $fighterOffensiveBonus;
        $this->fireControl[1] = $fighterOffensiveBonus;
        $this->fireControl[2] = $fighterOffensiveBonus;
    }
    
    protected function getAmmo($fireOrder)
    {
        return new $this->missileArray[$fireOrder->firingMode];
    }
    
    public function addAmmo($missileClass, $amount){
        foreach($this->missileArray as $missile){
            if(strcmp($missile->missileClass, $missileClass) == 0){
                $missile->setAmount($amount);
                break;
            }
        }
    }
    
    public function fire($gamedata, $fireOrder){
        $ammo = $this->missileArray[$fireOrder->firingMode];
        
        if($ammo->amount > 0){
            $ammo->amount--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, TacGamedata::$currentGameID, $this->firingMode, $ammo->amount);
        }
        else{
            
            $fireOrder->notes = "No ammo available of the selected type.";
            $fireOrder->updated = true;
            return;
        }
        
        $ammo->fire($gamedata, $fireOrder);
    }
    
    public function getDamage($fireOrder)
    {
        $ammo = $this->missileArray[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    
    public function setMinDamage(){ 0;}
    public function setMaxDamage(){ 0;}     
}

class ReloadRack extends ShipSystem
{
    // This needs to be implemented
    public $name = "ReloadRack";
    public $displayName = "Reload Rack (tbd)";
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);

    }
}