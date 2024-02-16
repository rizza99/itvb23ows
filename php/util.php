<?php

$GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

function isNeighbour($a, $b) {
    $a = explode(',', $a);
    $b = explode(',', $b);
    if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) return true;
    if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) return true;
    if ($a[0] + $a[1] == $b[0] + $b[1]) return true;
    return false;
}

function hasNeighBour($a, $board) {
    foreach (array_keys($board) as $b) {
        if (isNeighbour($a, $b)) return true;
    }
}

function has5NeighBours($a, $board) {
    $neighbors = 0;
    foreach (array_keys($board) as $b) {
        if (isNeighbour($a, $b)) $neighbors++;
    }
    if($neighbors >= 5) return true;
    return false;
}
function getNeighbours($location){
    $neighbours = [];
    $locationParts = explode(',', $location);
    foreach ($GLOBALS['OFFSETS'] as $offset) {
        $neighbours[] = ($locationParts[0] + $offset[0]) . ',' . ($locationParts[1] + $offset[1]);
    }
    return $neighbours;
}


function neighboursAreSameColor($player, $a, $board) {
    foreach ($board as $b => $st) {
        if (!$st) continue;
        $c = $st[count($st) - 1][0];
        if ($c != $player && isNeighbour($a, $b)) return false;
    }
    return true;
}

function len($tile) {
    return $tile ? count($tile) : 0;
}

function slide($board, $from, $to) {
    if (!hasNeighBour($to, $board)) return false;
    if (!isNeighbour($from, $to)) return false;
    $b = explode(',', $to);
    $common = [];
    foreach ($GLOBALS['OFFSETS'] as $pq) {
        $p = $b[0] + $pq[0];
        $q = $b[1] + $pq[1];
        if (isNeighbour($from, $p.",".$q)) $common[] = $p.",".$q;
    }
    if (!isset($board[$common[0]]) && !isset($board[$common[1]]) && !isset($board[$from]) && !isset($board[$to])) return false;
    return min(len($board[$common[0]]), len($board[$common[1]])) <= max(len($board[$from]), len($board[$to]));
}

function canpass($board,$player){

}

function validmove($tile){
switch($tile[1]){
    case 'G':
        
        $explodedFrom = explode(',', $from);
        $explodedTo = explode(',', $to);

        $distance = [$explodedTo[0] - $explodedFrom[0], $explodedTo[1] - $explodedFrom[1]];

        if (!(($distance[0] == 0 && $distance[1] != 0) || ($distance[1] == 0 && $distance[0] != 0) || ($distance[0] == $distance[1]))) {
            return false;
        }

        if (isNeighbour($from, $to)){
            return false;
        }

        $p = $explodedFrom[0] + $distance[0];
        $q = $explodedFrom[1] + $distance[1];

        while ($p != $explodedTo[0] || $q != $explodedTo[1]) {
            $pos = $p . "," . $q;

            if (isset($board[$pos])) {
                return false;
            }

            $p += $distance[0];
            $q += $distance[1];
        }
        
        break;

    case 'A':
        if (has5NeighBours($to,$board)){
            return false;
        }
        break;

    case 'S':
        $fromCoords = array_map('intval', explode(',', $from));
        $visited = [$from => true];
        $validMoves = [$fromCoords];
    
        for ($i = 0; $i < 3; $i++) {
            $newValidMoves = [];
    
            foreach ($validMoves as $coords) {
                $neighbours = getNeighbours(implode(',', $coords));
    
                foreach ($neighbours as $neighbour) {
                    if (!isset($board[$neighbour]) && !isset($visited[$neighbour])) {
                        $neighbourCoords = array_map('intval', explode(',', $neighbour));
                        $newValidMoves[] = $neighbourCoords;
                        $visited[$neighbour] = true;
                    }
                }
            }
    
            $validMoves = $newValidMoves;
    
            if (empty($validMoves)) {
                return false;
            }
        }
        $validmove = 0;
        foreach ($validMoves as $coords) {
            if (implode(',', $coords) === $to) {
                $validmove = 1;
            }
        }
        if ($validmove != 1){
            return false;
        }
        break;
        

    default:
        break;

}
return true;
}
?>