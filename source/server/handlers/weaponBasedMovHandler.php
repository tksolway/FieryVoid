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
        $this->fireOrders = array();
        
        foreach($ships as $ship){
            $fireOrders = $ship->getAllFireOrders();

            foreach ($fireOrders as $i=>$fireOrder){
                $weapon = $ship->getSystemById($fireOrder->weaponid);
                if($weapon->firesInPhase(31)){
                    if($weapon instanceof GraviticMine){
                        $fireOrders[$i]->shotshit++;
                        $fireOrders[$i]->rolled = 1;
                        $fireOrders[$i]->updated = true; 
                    }
         
                    $this->addFireOrder($fireOrder);
                }
            }
        }
    }
    
    private function addFireOrder($fireOrder){
        $this->fireOrders[] = $fireOrder;
    }
    
    public function isPhaseNeeded(){
        // At the moment, the phase isn't needed. No other weapons than
        // the mines are currently in.
        // return (sizeof($this->fireOrders) > 0);
        return false;
    }
    
    public function handleDamage($tacGameData){
        $mineOrders = array();

        foreach($this->fireOrders as $fireOrder){
            $shooter = $tacGameData->getShipById($fireOrder->shooterid);
            if($shooter->getSystemById($fireOrder->weaponid) instanceof GraviticMine){
                $mineOrders[] = $fireOrder;
            }
        }

        if((sizeof($mineOrders) > 0)){
            $mineHandler = new GravMineHandler($mineOrders);
            $mineHandler->doGravMineDamage($tacGameData);
        }
    }
    
    public function handleMovement($ships, $tacGameData){
        Debug::log("a");
        
        switch($tacGameData->phase){
            case 2:
                Debug::log("b");
                // Check for fire orders should already have been done.
                //$this->checkForFireOrders($ships);
                $mineOrders = array();

                foreach($this->fireOrders as $fireOrder){
                    $shooter = $tacGameData->getShipById($fireOrder->shooterid);
                    if($shooter->getSystemById($fireOrder->weaponid) instanceof GraviticMine){
                        $mineOrders[] = $fireOrder;
                    }
                }

                if((sizeof($mineOrders) > 0)){
        Debug::log("c");
                    $mineHandler = new GravMineHandler($mineOrders);
        Debug::log("d");
                    $mineHandler->doGravMineMoves($tacGameData);
        Debug::log("e");
                }
                break;
            
            case 31:
                break;
            
            default:
                Debug::log("*** WEIRD PHASE IN handleWeaponBasedMovement ***");
                break;
        }
    }
}

class GravMineHandler{
    private $gravMineFireOrders = array();
    
    function __construct($mineFireOrders){
        $this->gravMineFireOrders = $mineFireOrders;
    }
    
    public function doGravMineDamage($gamedata){
        $affectedShips = $this->getAffectedShips($gamedata);
        
        // Now it is time to filter out the ones that are caught in a triangle
        // of mines, or those that are exactly between two mines
        foreach($affectedShips as $shipId=>$mineOrderArray){
            $ship = $gamedata->getShipById($shipId);
             
            if(sizeof($mineOrderArray) == 1){
                // Only caught in one blast. This one is staying in the array
                continue;
            }else if(sizeof($mineOrderArray) == 2){
                // Caught by 2 blasts. Check if the ship might be directly
                // in between them
                if($this->isHexagonCrossed($ship, $mineOrderArray[0], $mineOrderArray[1])){
                    // The ship doesn't move but gets damage. It's caught in the middle
                    $this->damageShipFromMines($ship, $mineOrderArray, $gamedata);
                }
            }else{
                $shipCo = $ship->getCoPos();
                $pointsArray = array();
                // Caught by 3 or more blasts.
                foreach($mineOrderArray as $mineFireOrder){
                    $mineOrderArrayPixel = Mathlib::hexCoToPixel($mineFireOrder->x, $mineFireOrder->y);
                    $pointsArray[] = new Point($mineOrderArrayPixel["x"], $mineOrderArrayPixel["y"]);
                }

                $convexHull = new ConvexHull($pointsArray);
                $surroundingPoints = $convexHull->getHullPoints();

                $hexCorners = $this->getHexagonCornersPixel($shipCo["x"], $shipCo["y"]);
                
                foreach($hexCorners as $hexCorner){
                    if($this->checkForPointInBlastArea($hexCorner, $surroundingPoints)){
                        // Ship is in mine area
                        $this->damageShipFromMines($ship, $mineOrderArray, $gamedata);
                        continue;
                    }
                }
            }
        }
    }

    public function doGravMineMoves($gamedata){
        Debug::log("f");
        $affectedShips = $this->getAffectedShips($gamedata);
        Debug::log("g");
        
        // Now it is time to filter out the ones that are caught in a triangle
        // of mines, or those that are exactly between two mines
        foreach($affectedShips as $shipId=>$mineOrderArray){
            $ship = $gamedata->getShipById($shipId);
             
            if(sizeof($mineOrderArray) == 1){
                // Only caught in one blast. This one is staying in the array
                $this->moveShipToMine($ship, $mineOrderArray);
                continue;
            }else if(sizeof($mineOrderArray) == 2){
                Debug::log("******* 2 gravmines *******");
                // Caught by 2 blasts. Check if the ship might be directly
                // in between them
                if($this->isHexagonCrossed($ship, $mineOrderArray[0], $mineOrderArray[1])){
                    // The ship doesn't move but gets damage. It's caught in the middle
                    Debug::log("******* Caught between 2 gravmines *******");
                    continue;
                }else{
                    Debug::log("******* Affected by 2 gravmines *******");
                    // the ship moves.
                    $this->moveShipToMine($ship, $mineOrderArray);
                }
            }else{
                $shipCo = $ship->getCoPos();
                $pointsArray = array();
                // Caught by 3 or more blasts.
                foreach($mineOrderArray as $mineFireOrder){
                    $mineOrderArrayPixel = Mathlib::hexCoToPixel($mineFireOrder->x, $mineFireOrder->y);
                    $pointsArray[] = new Point($mineOrderArrayPixel["x"], $mineOrderArrayPixel["y"]);
                }

                $convexHull = new ConvexHull($pointsArray);
                $surroundingPoints = $convexHull->getHullPoints();

                $hexCorners = $this->getHexagonCornersPixel($shipCo["x"], $shipCo["y"]);
                
                foreach($hexCorners as $hexCorner){
                    if($this->checkForPointInBlastArea($hexCorner, $surroundingPoints)){
                        // Ship is in mine area
                        continue;
                    }
                }
                
                $this->moveShipToMine($ship, $mineOrderArray);
            }
        }
    }
    
    // Create an array of ship ids with the grav mine fire orders that hit
    // that particular ship id.
    private function getAffectedShips($gamedata){
        $affectedShips = array();
        $shipsInBlast = array();
        
        Debug::log("h");
        foreach($this->gravMineFireOrders as $gravMineFireOrder){
            $shooter = $gamedata->getShipById($gravMineFireOrder->shooterid);
            $gravMine = $shooter->getSystemById($gravMineFireOrder->weaponid);
            
            // Check the ships in the blast of each GravMine
            $shipsInBlast = $gravMine->getShipsInBlast($gamedata, $gravMineFireOrder);
            
            foreach($shipsInBlast as $ship){
                // For each ship ID, add the FireOrder that that ship is caught
                // in to the array at the index of the ship id.
                
                if(isset($affectedShips[$ship->id])){
                    // the ship id is already in the array. Add the fireOrder
                    // to the array.
                    
                    // First check if there is already a mine in the array of the current
                    // ship. If so, ignore it.
                    if($this->arrayHasMineOrderOnHex($gravMineFireOrder, $affectedShips[$ship->id])){
                        continue;
                    }
                    
                    $affectedShips[$ship->id][] = $gravMineFireOrder;
                }else{
                    // The ship id is not yet in the array. Make a new fireOrder
                    // array.
                    $fireOrders = array();
                    $fireOrders[] = $gravMineFireOrder;
                    $affectedShips[$ship->id] = $fireOrders;
                }
            }
        }
        
        return $affectedShips;
    }
    
    // Method to check if a mine on that hex is already in the array.
    // Return true if there is a mine there
    // Return false if there isn't.
    private function arrayHasMineOrderOnHex($mineOrder, $mineOrderArray){
        foreach($mineOrderArray as $order){
            if($order->x == $mineOrder->x && $order->y == $mineOrder->y){
                return true;
            }
        }
        
        return false;
    }
    
    // Returns the closest mine to the ship.
    private function getClosestMineCo($ship, $mineOrderArray){
        $retMineOrder = $mineOrderArray[0];
        $mineCo = Mathlib::hexCoToPixel($retMineOrder->x, $retMineOrder->y);
        $shipCo = $ship->getCoPos();
        $distance = Mathlib::getDistanceHex($mineCo, $shipCo);
        
        // First find the closest mine
        foreach($mineOrderArray as $mineOrder){
            $curMineCo = Mathlib::hexCoToPixel($mineOrder->x, $mineOrder->y);
            
            if(Mathlib::getDistanceHex($curMineCo, $shipCo) < $distance){
                $retMineOrder = $mineOrder;
                $mineCo = $curMineCo;
                $distance = Mathlib::getDistanceHex($mineCo, $shipCo);
            }
        }
        
        return $retMineOrder;
    }
    
    private function getGravMineDamage($ship, $distance){
        //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
        switch($ship->shipSizeClass){
            case -1:
                // fighter unit
                return $distance;
            case 0:
                // LCV
                return 2*$distance;
            case 1:
                // MCV
                return 3*$distance;
            case 2:
                // HCV
                return 4*$distance;
            case 3:
                // Capital
                return 5*$distance;
            case 4:
                // enormous non-base
                return 6*$distance;
            default:
                return 0;
        }
    }
    
    // Enters damage from the closest mine
    private function damageShipFromMines($ship, $mineOrderArray, $gamedata){
        // First get closest
        $closestMineOrder = $this->getClosestMineCo($ship, $mineOrderArray);
        $mineCo = Mathlib::hexCoToPixel($closestMineOrder->x, $closestMineOrder->y);
        $distance = round(Mathlib::getDistanceHex($mineCo, $ship->getCoPos()));
        $shooter = $gamedata->getShipById($closestMineOrder->shooterid);
        
        if($distance < 0 || $distance > 5){
            Debug::log("Strange distance in damageShipFromMines in weaponBasedMovHandler");
            return;
        }
        
        $damage = $this->getGravMineDamage($ship, $distance);
        $weapon = $shooter->getSystemById($closestMineOrder->weaponid);
        

            $weapon->damage($ship, $shooter, $closestMineOrder, $mineCo, $gamedata, $damage);
        
    }
    
    // Moves a ship towards the closest mine
    private function moveShipToMine($ship, $mineOrderArray){
        // First check if perhaps there is already a gslip listed for this ship
        // in the current turn. If so, just return. This slip has already been entered.
        // (Could happen when the page is reloaded.
        $shipmoves = $ship->getMovements(TacGamedata::$currentTurn);
        
        foreach($shipmoves as $move){
            if($move->type === "gslip" && $move->turn == TacGamedata::$currentTurn){
                return;
            }
        }

        // Get the coordinate of the closest mine.
        $closestMineOrder = $this->getClosestMineCo($ship, $mineOrderArray);
        $mineCo = Mathlib::hexCoToPixel($closestMineOrder->x, $closestMineOrder->y);
        
        // Check in what direction the ship should move.
        // Check angle, and depending on that, make a movement order
        $heading = Mathlib::getCompassHeadingOfPoint($ship->getCoPos(), $mineCo );
        // Make certain $heading in only in multitudes of 60 degrees
        $heading60 = 60*floor(($heading+30)/60);
        $headingNr = floor(($heading+30)/60);
        $lastmove = $ship->getLastMovement();
        
        Debug::log("heading of gravmine = ".$heading);
        Debug::log("heading60 of gravmine = ".$heading60);
        Debug::log("headingNr of gravmine = ".$headingNr);
        
        // Math to calculate. Add 30 to line up hexes. Then integer division by 60 gives the right hex.
        //$hexDirection = floor(($heading+30)/60);
        // This call doesn't work: $newPos = Mathlib::getHexToDirection($heading60, $lastmove->x, $lastmove->y);
        Debug::log("lastmove x = ".$lastmove->x." y = ".$lastmove->y);
        
        $newPos = array("x" => $lastmove->x,"y" => $lastmove->y);
        Debug::log("newpos x = ".$newPos["x"]." y = ".$newPos["y"]);
        
        switch($headingNr){
            case 0:
                $newPos["x"] = $newPos["x"] + 1;
                break;
            case 1:
                if($newPos["y"]%2 == 0){
                    // the y-coordinate is even
                    $newPos["x"] = $newPos["x"] + 1;
                    $newPos["y"] = $newPos["y"] + 1;
                }else{
                    $newPos["y"] = $newPos["y"] + 1;
                }
                break;
            case 2:
                if($newPos["y"]%2 == 0){
                    // the y-coordinate is even
                    $newPos["y"] = $newPos["y"] + 1;
                }else{
                    $newPos["x"] = $newPos["x"] - 1;
                    $newPos["y"] = $newPos["y"] + 1;
                }
                break;
            case 3:
                $newPos["x"] = $newPos["x"] - 1;
                break;
            case 4:
                if($newPos["y"]%2 == 0){
                    // the y-coordinate is even
                    $newPos["y"] = $newPos["y"] - 1;
                }else{
                    $newPos["x"] = $newPos["x"] - 1;
                    $newPos["y"] = $newPos["y"] - 1;
                }
                break;
            case 5:
                if($newPos["y"]%2 == 0){
                    // the y-coordinate is even
                    $newPos["x"] = $newPos["x"] + 1;
                    $newPos["y"] = $newPos["y"] - 1;
                }else{
                    $newPos["y"] = $newPos["y"] - 1;
                }
                break;
        }
        
        $movements = array();
        $movement = new MovementOrder(null, "gslip", $newPos["x"], $newPos["y"], 0, 0, $lastmove->speed, $lastmove->heading, $lastmove->facing, false, TacGamedata::$currentTurn, 0, $ship->iniative);
        
        $movements[] = $movement;
        
        Manager::submitGraviticMove(TacGamedata::$currentGameID, $ship->id, TacGamedata::$currentTurn, $movements);
    }
    
    // Checks if a given hexagon is crossed by a line
    private function isHexagonCrossed($affectedShip, $mine1, $mine2){
        Debug::log("isHexagonCrossed 1");

        $shipCo = $affectedShip->getCoPos();
        Debug::log("isHexagonCrossed 2");
        $hexCorners = $this->getHexagonCornersPixel($shipCo["x"], $shipCo["y"]);
        Debug::log("isHexagonCrossed 3");
        
        $mine1Co = Mathlib::hexCoToPixel($mine1->x, $mine1->y);
        $mine1CoPoint = new Point($mine1Co["x"], $mine1Co["y"]);
        $mine2Co = Mathlib::hexCoToPixel($mine2->x, $mine2->y);
        $mine2CoPoint = new Point($mine2Co["x"], $mine2Co["y"]);

        // Create the two points of the mines
        Debug::log("isHexagonCrossed 4");
        
        // Check if the line between the mines crosses any of the sides of the hex
        Debug::log("Ship position: x: ".$shipCo["x"]." y: ".$shipCo["y"]);
        foreach($hexCorners as $i=>$hexCorner){
             Debug::log("Hex corner ".$i." : x: ".$hexCorner->x." y: ".$hexCorner->y);
        }
       
        Debug::log("mine1Point: x: ".$mine1CoPoint->x." y: ".$mine1CoPoint->y);
        Debug::log("mine2Point: x: ".$mine2CoPoint->x." y: ".$mine2CoPoint->y);
        if($this->checkLineIntersection($hexCorners[0], $hexCorners[1], $mine1CoPoint, $mine2CoPoint)
           || $this->checkLineIntersection($hexCorners[1], $hexCorners[2], $mine1CoPoint, $mine2CoPoint)
           || $this->checkLineIntersection($hexCorners[2], $hexCorners[3], $mine1CoPoint, $mine2CoPoint)
           || $this->checkLineIntersection($hexCorners[3], $hexCorners[4], $mine1CoPoint, $mine2CoPoint)
           || $this->checkLineIntersection($hexCorners[4], $hexCorners[5], $mine1CoPoint, $mine2CoPoint)
           || $this->checkLineIntersection($hexCorners[5], $hexCorners[0], $mine1CoPoint, $mine2CoPoint)
                ){
        Debug::log("isHexagonCrossed 5");
            return true;
        }
        
        Debug::log("isHexagonCrossed 6");
        return false;
    }
    
    // Takes the centre of a hex in pixel coordinates and returns
    // an array with the pixel coordinate points of the corners of that hex
    private function getHexagonCornersPixel($px, $py){
        $corners = array();
        
        $hexHeight = 50; // Half the height of a hex
        $cos30 = cos(M_PI/12);
        $sin30 = sin(M_PI/12);
        
        // starting at point straight up from center. (at 90 degrees)
        // Going clockwise.
        $corners[] = new Point($px, $py+$hexHeight );
        $corners[] = new Point($px + $hexHeight*$cos30, $py + $hexHeight*$sin30);
        $corners[] = new Point($px + $hexHeight*$cos30, $py - $hexHeight*$sin30);
        $corners[] = new Point($px, $py-$hexHeight );
        $corners[] = new Point($px - $hexHeight*$cos30, $py - $hexHeight*$sin30);
        $corners[] = new Point($px - $hexHeight*$cos30, $py + $hexHeight*$sin30);
        
        return $corners;
    }

    private function checkForPointInBlastArea($point, $corners){
        // This method is inspired by the code written by:
        // Michaël Niessen (2009) as presented on Website: http://AssemblySys.com
        // If you find this script useful, you can show your
        // appreciation by getting Michaël a cup of coffee ;)
        // PayPal: michael.niessen@assemblysys.com
        // 
        // First check if the point is on one of the corners
        foreach($corners as $corner) {
            if($point->x == $corner->x && $point->y == $corner->y)
                return true;
        }
 
        // Check if the point is inside the polygon or on the boundary
        $intersections = 0; 
        $corners_count = count($corners);
 
        for ($i=1; $i < $corners_count; $i++) {
            $corner1 = $corners[$i-1]; 
            $corner2 = $corners[$i];
            if ($corner1->y == $corner2->y and $corner1->y == $point->y and $point->x > min($corner1->x, $corner2->x) and $point->x < max($corner1->x, $corner2->x)) { // Check if point is on an horizontal polygon boundary
                return true;
            }
            if ($point->y > min($corner1->y, $corner2->y) and $point->y <= max($corner1->y, $corner2->y) and $point->x <= max($corner1->x, $corner2->x) and $corner1->y != $corner2->y) { 
                $xinters = ($point->y - $corner1->y) * ($corner2->x - $corner1->x) / ($corner2->y - $corner1->y) + $corner1->x; 
                if ($xinters == $point->x) { // Check if point is on the polygon boundary (other than horizontal)
                    return true;
                }
                if ($corner1->x == $corner2->x || $point->x <= $xinters) {
                    $intersections++; 
                }
            } 
        } 
        // If the number of edges we passed through is odd, then it's in the polygon. 
        if ($intersections % 2 != 0) {
            return true;
        } else {
            return false;
        }
    }
    
    private function checkLineIntersection($p1, $p2, $p3, $p4){
        // calculates intersection and checks for parallel lines.  
        // also checks that the intersection point is actually on  
        // the line segment p1-p2  

        // calculate differences  
        $xD1=$p2->x-$p1->x;  
        $xD2=$p4->x-$p3->x;  
        $yD1=$p2->y-$p1->y;  
        $yD2=$p4->y-$p3->y;  
        $xD3=$p1->x-$p3->x;  
        $yD3=$p1->y-$p3->y;    

        // calculate the lengths of the two lines  
        $len1=sqrt($xD1*$xD1+$yD1*$yD1);  
        $len2=sqrt($xD2*$xD2+$yD2*$yD2);  

        // calculate angle between the two lines.  
        $dot=($xD1*$xD2+$yD1*$yD2); // dot product  
        $deg=$dot/($len1*$len2);  

        // if abs(angle)==1 then the lines are parallell,  
        // so no intersection is possible  
        if(abs($deg)==1) return false;  

        // find intersection Pt between two lines  
        $pt=new Point(0,0);  
        $div=$yD2*$xD1-$xD2*$yD1;  
        $ua=($xD2*$yD3-$yD2*$xD3)/$div;  
        $ub=($xD1*$yD3-$yD1*$xD3)/$div;  
        $pt->x=$p1->x+$ua*$xD1;  
        $pt->y=$p1->y+$ua*$yD1;  

        // calculate the combined $length of the two $segments  
        // between Pt-p1 and Pt-p2  
        $xD1=$pt->x-$p1->x;  
        $xD2=$pt->x-$p2->x;  
        $yD1=$pt->y-$p1->y;  
        $yD2=$pt->y-$p2->y;  
        $segmentLen1=sqrt($xD1*$xD1+$yD1*$yD1)+sqrt($xD2*$xD2+$yD2*$yD2);  

        // calculate the combined $length of the two $segments  
        // between Pt-p3 and Pt-p4  
        $xD1=$pt->x-$p3->x;  
        $xD2=$pt->x-$p4->x;  
        $yD1=$pt->y-$p3->y;  
        $yD2=$pt->y-$p4->y;  
        $segmentLen2=sqrt($xD1*$xD1+$yD1*$yD1)+sqrt($xD2*$xD2+$yD2*$yD2);  

        // if the $lengths of both sets of $segments are the same as  
        // the $lenghts of the two lines the point is act$uall$y  
        // on the line $segment.  

        // if the point isn’t on the line, return null  
        if(abs($len1-$segmentLen1)>0.01 || abs($len2-$segmentLen2)>0.01)  
            return false;  

        // return the valid intersection  
        return true;  
    }  
}

class Point{
    public $x;
    public $y;
    
    function __construct($x, $y){
        $this->x = $x;
        $this->y = $y;
    }    
}


/**
 * Convex hull calculator
 *
 * convex_hull is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * convex_hull is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with convex_hull; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Representation of a convex hull, which is calculated based on a given set of 
 * points.
 *
 * The algorithm used to calculate the convex hull is QuickHull.
 * 
 * @author Jakob Westhoff <jakob@php.net>
 * @license GPLv3
 */
class ConvexHull 
{
    /**
     * Set of points provided as input for the calculation.
     * 
     * @var array( Point )
     */
    protected $inputPoints;

    /**
     * The points of the convex hull after the quickhull algorithm has been 
     * executed. 
     * 
     * @var array( Point )
     */
    protected $hullPoints;


    /**
     * Construct a new ConvexHull object using the given points as input. 
     * 
     * @param array $points 
     */
    public function __construct( array $pofloats ) 
    {
        $this->inputPoints = $pofloats;
        $this->hullPoints = null;
    }

    /**
     * Return the pofloats of the convex hull.
     *
     * The pofloats will be ordered to form a clockwise defined polygon path 
     * around the convex hull. 
     * 
     * @return array( Point );
     */
    public function getHullPoints() 
    {
        if ( $this->hullPoints === null ) 
        {
            // Initial run with max and min x value points. 
            // These points are guaranteed to be points of the convex hull
            // Initially the points on both sides of the line are processed.
            $maxX = $this->getMaxXPoint();
            $minX = $this->getMinXPoint();
            $this->hullPoints = array_merge( 
                $this->quickHull( $this->inputPoints, $minX, $maxX ),
                $this->quickHull( $this->inputPoints, $maxX, $minX )
            );
        }

        return $this->hullPoints;
    }

    /**
     * Return the points provided as input point set. 
     * 
     * @return array( Point )
     */
    public function getInputPoints() 
    {
        return $this->inputPoints;
    }

    /**
     * Find and return the point with the maximal X value. 
     * 
     * @return Point
     */
    protected function getMaxXPoint() 
    {
        $max = $this->inputPoints[0];
        foreach( $this->inputPoints as $p ) 
        {
            if ( $p->x > $max->x ) 
            {
                $max = $p;
            }
        }
        return $max;
    }

    /**
     * Find and return the point with the minimal X value. 
     * 
     * @return Point
     */
    protected function getMinXPoint() 
    {
        $min = $this->inputPoints[0];
        foreach( $this->inputPoints as $p ) 
        {
            if ( $p->x < $min->x ) 
            {
                $min = $p;
            }
        }
        return $min;
    }

    /**
     * Calculate a distance indicator between the line defined by $start and 
     * $end and an arbitrary $point.
     *
     * The value returned is not the correct distance value, but is sufficient 
     * to determine the point with the maximal distance from the line. The 
     * returned distance indicator is therefore directly relative to the real 
     * distance of the point. 
     *
     * The returned distance value may be positive or negative. Positive values 
     * indicate the point is left of the specified vector, negative values 
     * indicate it is right of it. Furthermore if the value is zero the point 
     * is colinear to the line.
     * 
     * @param float $start 
     * @param float $end 
     * @param float $point 
     * @return float
     */
    protected function calculateDistanceIndicator( $start, $end, $point ) 
    {
        /*
         * The real distance value could be calculated as follows:
         * 
         * Calculate the 2D Pseudo crossproduct of the line vector ($start 
         * to $end) and the $start to $point vector. 
         * ((y2*x1) - (x2*y1))
         * The result of this is the area of the parallelogram created by the 
         * two given vectors. The Area formula can be written as follows:
         * A = |$start->$end| * h
         * Therefore the distance or height is the Area divided by the length 
         * of the first vector. This division is not done here for performance 
         * reasons. The length of the line does not change for each of the 
         * comparison cycles, therefore the resulting value can be used to 
         * finde the point with the maximal distance without performing the 
         * division.
         *
         * Because the result is not returned as an absolute value its 
         * algebraic sign indicates of the point is right or left of the given 
         * line.
         */

        $vLine = array( 
            $end->x - $start->x,
            $end->y - $start->y
        );

        $vPoint = array( 
            $point->x - $start->x,
            $point->y - $start->y
        );

        return ( ( $vPoint[1] * $vLine[0] ) - ( $vPoint[0] * $vLine[1] ) );
    }

    /**
     * Calculate the distance indicator for each given point and return an 
     * array containing the point and the distance indicator. 
     *
     * Only points left of the line will be returned. Every point right of the 
     * line or colinear to the line will be deleted.
     * 
     * @param array $start 
     * @param array $end 
     * @param array $points 
     * @return array( Point )
     */
    protected function getPointDistanceIndicators( $start, $end, array $points ) 
    {
        $resultSet = array();

        foreach( $points as $p ) 
        {
            if ( ( $distance = $this->calculateDistanceIndicator( $start, $end, $p ) ) > 0 ) 
            {
                $resultSet[] = array( 
                    'point'    => $p,
                    'distance' => $distance
                );
            }
            else 
            {
                continue;
            }
        }

        return $resultSet;
    }

    /**
     * Get the point which has the maximum distance from a given line.
     *
     * @param array $pointDistanceSet 
     * @return Point
     */
    protected function getPointWithMaximumDistanceFromLine( array $pointDistanceSet ) 
    {
        $maxDistance = 0;
        $maxPoint    = null;

        foreach( $pointDistanceSet as $p ) 
        {
            if ( $p['distance'] > $maxDistance )
            {
                $maxDistance = $p['distance'];
                $maxPoint    = $p['point'];
            }
        }

        return $maxPoint;
    }

    /**
     * Extract the points from a point distance set. 
     * 
     * @param array $pointDistanceSet 
     * @return array
     */
    protected function getPointsFromPointDistanceSet( $pointDistanceSet ) 
    {
        $points = array();

        foreach( $pointDistanceSet as $p ) 
        {
            $points[] = $p['point'];
        }

        return $points;
    }

    /**
     * Execute a QuickHull run on the given set of points, using the provided 
     * line as delimiter of the search space.
     *
     * Only points left of the given line will be analyzed. 
     * 
     * @param array $points 
     * @param array $start 
     * @param array $end 
     * @return array
     */
    protected function quickHull( array $points, $start, $end ) 
    {
        $pointsLeftOfLine = $this->getPointDistanceIndicators( $start, $end, $points );
        $newMaximalPoint = $this->getPointWithMaximumDistanceFromLine( $pointsLeftOfLine );
        
        if ( $newMaximalPoint === null ) 
        {
            // The current delimiter line is the only one left and therefore a 
            // segment of the convex hull. Only the end of the line is returned 
            // to not have points multiple times in the result set.
            return array( $end );
        }

        // The new maximal point creates a triangle together with $start and 
        // $end, Everything inside this trianlge can be ignored. Everything 
        // else needs to handled recursively. Because the quickHull invocation 
        // only handles points left of the line we can simply call it for the 
        // different line segements to process the right kind of points.
        $newPoints = $this->getPointsFromPointDistanceSet( $pointsLeftOfLine );
        return array_merge(
            $this->quickHull( $newPoints, $start, $newMaximalPoint ),
            $this->quickHull( $newPoints, $newMaximalPoint, $end )
        );
    }
}
?>
