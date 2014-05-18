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
        $this->fireOrders[] = $fireOrder;
    }
    
    public function isPhaseNeeded(){
        return (sizeof($this->fireOrders) > 0);
    }
    
    public function handleWeaponBasedMovement($ships, $tacGameData){
        $mineOrders = array();
        
        foreach($this->fireOrders as $fireOrder){
            $shooter = $tacGameData->getShipById($fireOrder->shooterid);
            if($shooter->getSystemById($fireOrder->weaponid) instanceof GraviticMine){
                $mineOrders[] = $fireOrder;
            }
        }
        
        if((sizeof($mineOrders) > 0)){
            $mineHandler = new GravMineHandler($mineOrders);
            $mineHandler->doGravMineMoves($tacGameData);
        }

        // Set phase to firing phase
        $tacGameData->setPhase(3); 
        $tacGameData->setActiveship(-1);
        self::$dbManager->updateGamedata($tacGameData);
    }
}

class GravMineHandler{
    private $gravMineFireOrders = array();
    
    function __construct($mineFireOrders){
        $this->gravMineFireOrders = $mineFireOrders;
    }
    
    public function doGravMineMoves($gamedata){
        $affectedShips = array();
        $shipsInBlast = array();
        
        foreach($this->gravMineFireOrders as $gravMineFireOrder){
            $shooter = $gamedata->getShipById($gravMineFireOrder->shooterid);
            $gravMine = $shooter->getSystemById($gravMineFireOrder->weaponid);
            
            // Check the ships in the blast of each GravMine
            $shipsInBlast = $gravMine->getShipsInBlast($gamedata, $gravMineFireOrder);
            
            // GetShipsInBlast does not return anything!!!
            
            
            foreach($shipsInBlast as $ship){
                // For each ship ID, add the FireOrder that that ship is caught
                // in to the array at the index of the ship id.
Debug::log("DGMM: 9");
                if(isset($affectedShips[$ship->id])){
                    // the ship id is already in the array. Add the fireOrder
                    // to the array.
Debug::log("DGMM: 10");
                    $affectedShips[$ship->id][] = $gravMineFireOrder;
                }else{
                    // The ship id is not yet in the array. Make a new fireOrder
                    // array.
Debug::log("DGMM: 11");
                    $fireOrders = array();
                    $fireOrders[] = $gravMineFireOrder;
                    $affectedShips[$ship->id] = $fireOrders;
                }
            }
        }
Debug::log("DGMM: 12");
        
        if(sizeof($affectedShips) == 0){
            // There were no ships caught in any mines
            return null;
        }
        
        // There were ships caught in the mines.
        // Now it is time to filter out the ones that are caught in a triangle
        // of mines, or those that are exactly between two mines
        foreach($affectedShips as $shipId=>$mineOrderArray){
            if(sizeof($mineOrderArray) == 1){
                // Only caught in one blast. This one is staying in the array
                Debug::log("SHIP IS CAUGHT IN 1 MINES");
                continue;
            }else if(sizeof($mineOrderArray) == 2){
                // Caught by 2 blasts. Check if the ship might be directly
                // in between them
                $ship = $gamedata->getShipById($shipId);
                Debug::log("SHIP IS CAUGHT IN 2 MINES");
            }else{
                $ship = $gamedata->getShipById($shipId);
                $shipCo = $ship->getCoPos();
                $shipPosition = new Point($shipCo["x"], $shipCo["y"]);
                $pointsArray = array();
                // Caught by 3 or more blasts.
                foreach($mineOrderArray as $mineFireOrder){
                    $pointsArray = new Point($mineFireOrder->x, $mineFireOrder->y);
                    
                    $convexHull = new ConvexHull($pointsArray);
                    $surroundingPoints = $convexHull->getHullPoints();
                    
                    if($this->checkForPointInBlastArea($shipPosition, $surroundingPoints)){
                        // Ship is in mine area
                        Debug::log("SHIP IS CAUGHT IN MINES");
                    }else{
                        // Ship is outside mine area
                        Debug::log("SHIP IS OUTSIDE MINES");
                    }
                }
            }
        }
        
        return $affectedShips;
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
    
    public static function doGravMineDamage($ships){
        $affectedShips = array();
        
        return $affectedShips;
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
    protected function calculateDistanceIndicator( array $start, array $end, array $point ) 
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

        return ( ( $vPoint->y * $vLine->x ) - ( $vPoint->x * $vLine->y ) );
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
    protected function getPointDistanceIndicators( array $start, array $end, array $points ) 
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
    protected function quickHull( array $points, array $start, array $end ) 
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
