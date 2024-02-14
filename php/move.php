<?php

session_start();

include_once 'util.php';

$from = $_POST['from'];
$to = $_POST['to'];

$player = $_SESSION['player'];
$board = $_SESSION['board'];
$hand = $_SESSION['hand'][$player];
unset($_SESSION['error']);

if (!isset($board[$from]))
    $_SESSION['error'] = 'Board position is empty';
elseif ($board[$from][count($board[$from])-1][0] != $player)
    $_SESSION['error'] = "Tile is not owned by player";
elseif ($hand['Q'])
    $_SESSION['error'] = "Queen bee is not played";
else {
    $tile = array_pop($board[$from]);
    if (!hasNeighBour($to, $board))
        $_SESSION['error'] = "Move would split hive";
    else {
        $all = array_keys($board);
        $queue = [array_shift($all)];
        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach ($GLOBALS['OFFSETS'] as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];
                if (in_array("$p,$q", $all)) {
                    $queue[] = "$p,$q";
                    $all = array_diff($all, ["$p,$q"]);
                }
            }
        }

        switch($tile[1]){
            case 'G':
                
                $explodedFrom = explode(',', $from);
                $explodedTo = explode(',', $to);
        
                $distance = [$explodedTo[0] - $explodedFrom[0], $explodedTo[1] - $explodedFrom[1]];
        
                if (!(($distance[0] == 0 && $distance[1] != 0) || ($distance[1] == 0 && $distance[0] != 0) || ($distance[0] == $distance[1]))) {
                    $_SESSION['error'] = "The grasshopper cannot move like that";
                }
        
                if (isNeighbour($from, $to)){
                    $_SESSION['error'] = "The grasshopper has to jump over at least 1 other tile";
                }
        
                $p = $explodedFrom[0] + $distance[0];
                $q = $explodedFrom[1] + $distance[1];
        
                while ($p != $explodedTo[0] || $q != $explodedTo[1]) {
                    $pos = $p . "," . $q;
        
                    if (isset($board[$pos])) {
                        $_SESSION['error'] = "The grasshopper cannot move like that";
                    }
        
                    $p += $distance[0];
                    $q += $distance[1];
                }
                
                break;

            case 'A':
                if (has5NeighBours($to,$board)){
                    $_SESSION['error'] = "The ant cannot move there";
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
                        $_SESSION['error'] = "no valid moves for this spider";
                    }
                }
                $validmove = 0;
                foreach ($validMoves as $coords) {
                    if (implode(',', $coords) === $to) {
                        $validmove = 1;
                    }
                }
                if ($validmove != 1){
                    $_SESSION['error'] = "this spider cannot move there";
                }
                break;
                

            default:
                break;

        }

        if ($all) {
            $_SESSION['error'] = "Move would split hive";
        } else {
            if ($from == $to) $_SESSION['error'] = 'Tile must move';
            elseif (isset($board[$to]) && $tile[1] != "B") $_SESSION['error'] = 'Tile not empty';
            elseif ($tile[1] == "Q" || $tile[1] == "B") {
                if (!slide($board, $from, $to))
                    $_SESSION['error'] = 'Tile must slide';
            }
        }
    }
    if (isset($_SESSION['error'])) {
        if (isset($board[$from])) array_push($board[$from], $tile);
        else $board[$from] = [$tile];
    } else {
        if (isset($board[$to])) array_push($board[$to], $tile);
        else $board[$to] = [$tile];
        $_SESSION['player'] = 1 - $_SESSION['player'];
        $db = include 'database.php';
        $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "move", ?, ?, ?, ?)');
        $stmt->bind_param('issis', $_SESSION['game_id'], $from, $to, $_SESSION['last_move'], get_state());
        $stmt->execute();
        $_SESSION['last_move'] = $db->insert_id;
        unset($board[$from]);
    }
    $_SESSION['board'] = $board;
}

header('Location: index.php');

?>