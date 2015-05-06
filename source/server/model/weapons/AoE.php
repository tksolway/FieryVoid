<?php

    class AoE extends Weapon{

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function calculateHit($gamedata, $fireOrder){
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $shooterPos = $shooter->getCoPos();
            $target = array("x"=>$fireOrder->x, "y"=>$fireOrder->y);
            $hit = false;
            
            $fireOrder->needed = 0;
            $rolled = Dice::d(4);
            if ($rolled == 1){
                $rolled = Dice::d(10);
                if ($rolled<7){
                    $hit = true;
                    $dis = Dice::d(6);
                    
                    
                    $maxdis = mathlib::getDistanceHex($shooterPos, mathlib::hexCoToPixel($target["x"], $target["y"]));
                    if ($dis>$maxdis)
                        $dis = floor($maxdis);
                    
                    for ($i=0;$i<$dis;$i++){
                        $target = mathlib::getHexToDirection($rolled, $target["x"], $target["y"]);
                    }   
                    $fireOrder->x = $target["x"];
                    $fireOrder->y = $target["y"];
                    $fireOrder->pubnotes .= "Shot deviates $dis hexes. ";   
                    
                }else{
                    $fireOrder->pubnotes .= "Charge dissipates. ";  
                }
                
            }else{
                $hit = true;
            }
            
            if ($hit){
                $fireOrder->shotshit++;
            }
            
             
            
            $fireOrder->rolled = 1;//Marks that fire order has been handled
            $fireOrder->updated = true; 
        }
        
        public function fire($gamedata, $fireOrder){
        
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $shooterpos = $shooter->getCoPos();
            $target = array("x"=>$fireOrder->x, "y"=>$fireOrder->y);
            
            $this->calculateHit($gamedata, $fireOrder);
            if ($fireOrder->shotshit>0){
                    $pos = mathlib::hexCoToPixel($fireOrder->x, $fireOrder->y);
                    $ships1 = $gamedata->getShipsInDistance($pos);
                    
                    
                foreach($ships1 as $ship){
                    $this->AOEdamage($ship, $shooter, $fireOrder, $shooterpos, 30, $gamedata);
                    $fireOrder->notes .= $ship->name ." in same hex. "; 
                }
                
                
                $ships2 = $gamedata->getShipsInDistance($pos, mathlib::$hexWidth+1);
                 
                foreach($ships2 as $ship){
                    if (isset($ships1[$ship->id]))
                        continue;
                    
                    $fireOrder->notes .= $ship->name ." in adjacent hex. "; 
                    $this->AOEdamage($ship, $shooter, $fireOrder, $pos, 10, $gamedata);
                 }
            }
                  
        }
        
        public function AOEdamage($target, $shooter, $fireOrder, $pos, $amount, $gamedata){
        
            
            if ($target->isDestroyed())
                return;
            
            
            $amount -= $target->getDamageMod($shooter, $pos, $gamedata->turn);
            
            if ($target instanceof FighterFlight){
				$this->fighterDamage($target, $shooter, $fireOrder, $pos, $amount, $gamedata);
			}else{
				
						
				$system = $target->getHitSystem($pos, $shooter, $fireOrder, $this);
				
				if ($system == null)
					return;
					
				$this->doDamage($target, $shooter, $system, $amount, $fireOrder, $pos, $gamedata);
			}  
            
        
        }
        
        public function fighterDamage($target, $shooter, $fireOrder, $pos, $amount, $gamedata){
			
			foreach ($target->systems as $fighter){
				
				if ($fighter == null || $fighter->isDestroyed()){
					continue;
				}
				
				$this->doDamage($target, $shooter, $fighter, $amount, $fireOrder, $pos, $gamedata);
				
			}
			
		}

    }
    
    class EnergyMine extends AoE{
    
        public $name = "energyMine";
        public $displayName = "Energy mine";
        public $range = 50;
        public $loadingtime = 2;
        public $ballistic = true;
        public $hextarget = true;
        public $hidetarget = true;
        
        public $flashDamage = true;
        public $priority = 1;
        
            
        public $trailColor = array(141, 240, 255);
        public $animation = "ball";
        public $animationColor = array(141, 240, 255);
        public $animationExplosionScale = 1;
        public $animationExplosionType = "AoE";
        public $explosionColor = array(141, 240, 255);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }


    class GraviticMine extends AoE{
        public $name = "graviticMine";
        public $displayName = "Gravitic mine";
        public $range = 40;
        public $loadingtime = 2;
        public $ballistic = true;
        public $hextarget = true;
        public $hidetarget = true;
        public $flashDamage = true;

        public $trailColor = array(141, 240, 255);
        public $animation = "ball";
        public $animationColor = array(141, 240, 255);
        public $animationExplosionScale = 5;
        public $animationExplosionType = "GravMine";
        public $explosionColor = array(99, 255, 00);
//        public $explosionColor = array(40, 70, 20);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function calculateHit($gamedata, $fireOrder){
            // GravMines don't deviate nor do they dissipate. 
            $fireOrder->shotshit++;
            
            if(TacGamedata::$currentPhase == 3){
                // Only in the firing phase, is the mine completely handled.
                $fireOrder->rolled = 1;
                $fireOrder->updated = true; 
            }
        }

        public function getShipsInBlast($gamedata, $fireOrder){
            Debug::log("**** GravMine: getShipsInBlast ****");
            $pos = mathlib::hexCoToPixel($fireOrder->x, $fireOrder->y);
            Debug::log("fireOrder x: ",$fireOrder->x);
            Debug::log("fireOrder y: ",$fireOrder->y);

            $this->calculateHit($gamedata, $fireOrder);
            
            Debug::log("**** End: getShipsInBlast ****");
            return $gamedata->getShipsInDistance($pos, mathlib::$hexWidth*5+1);
        }
        
        public function fire($gamedata, $fireOrder){
            // All the relevant damage dealing has already been done
            // by the GravMineHandler.
            return;
        }
        
        protected function getSystemArmour($system, $gamedata, $fireOrder){
            // grav mines ignore armor
            return 0;
	}

        public function damage($ship, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){

             if($ship instanceof FighterFlight){
                foreach ($ship->systems as $fighter){

                    if ($fighter == null || $fighter->isDestroyed()){
                            continue;
                    }

                    $this->doDamage($ship, $shooter, $fighter, $damage, $fireOrder, $pos, $gamedata);
                }
            }else{
                $system = $ship->getHitSystem($pos, $shooter, $fireOrder, $this);

                if ($system == null){
                Debug::log("**** No system found for gravmine damage ****");


                        return;
                }

                Debug::log("*****************************************");
                Debug::log("Doing damage on system: ".$system->displayName);
                Debug::log("Amount of damage: ".$damage);
                Debug::log("*****************************************");

                $this->doDamage($ship, $shooter, $system, $damage, $fireOrder, $pos, $gamedata);
            }
        }
       
        public function firesInPhase($phase) {
            switch($phase){
                case 3:
                case 31:
                    return true;
                default:
                    return true;
            }
        }
        
        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    }
?>
