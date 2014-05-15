<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WeaponBasedMovHandler{
    private $fireOrders = array();
    
    function __construct($gamedata){
        
    }
        
    public function checkForFireOrders($ships){
        foreach($ships as $ship){
            $fireOrders = $ship->getAllFireOrders();

            foreach ($fireOrders as $fireOrder){
                if($ship->getSystemById($fireOrder->weaponid)->firesInPhase(31)){
                    $this->addFireOrder($fireOrder);
                }
            }
        }
    }
    
    private function addFireOrder($fireOrder){
        $this->fireOrders = $fireOrder;
    }
    
    public function isPhaseNeeded(){
        return (sizeof($this->fireOrders) > 0);
    }
    
    public function handleWeaponBasedMovement($ships, $tacGameData){
        // Set phase to firing phase
        $gamedata->setPhase(3); 
        $gamedata->setActiveship(-1);
        self::$dbManager->updateGamedata($gamedata);
    }
}

class GravMineHandler{
    private $gravMines = array();
    
    function __construct($mines){
        $this->gravMines = $mines;
    }
    
    public static function doGravMineMoves($gravMines){
        $affectedShips = array();
        
        return $affectedShips;
    }

    public static function doGravMineDamage($gravMines){
        $affectedShips = array();
        
        return $affectedShips;
    }
}
?>
