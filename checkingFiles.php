<?php

include 'config/classes.php';

$nodes = array();
$board = NULL;
$net_count = -1;

// We need all the 6 files to be uploaded
function generateData($folderName){

    global $nodes;
    global $board;
    global $net_count;
    $node_count = 0;
    $net_count = 0;
    //$folderName = 'uploads';

    $file_paths = findFiles($folderName);


    $nodes     = generateNodes($file_paths['nodes']);
    $nodes     = getNodePositions($nodes, $file_paths['pl']);
    $arrData   = assignNetsToNodes($nodes, $file_paths['nets']);
    $nodes     = $arrData['nodes'];
    $net_count = $arrData['net_count'];
    $board     = getBoardData($file_paths['scl']);
    $board     = new Board($board['width'], $board['rows_height'], $board['n_rows']);

}

function findFiles($folderName){
    $files = scandir("./$folderName");
    $allowed = array('aux', 'nets', 'nodes', 'pl', 'scl');

    foreach($allowed as $allowed_type){
        $pattern = "~(\w|\d)+.$allowed_type~";
        foreach($files as $file){
            if(preg_match($pattern, $file)){
                $file_path[$allowed_type] = $folderName.'/' . $file; 
                break;
            }
        }
    }

    return $file_path;
}

function getBoardData($fullPath){
    $handle = fopen($fullPath, "r");
    $patterns = ['~(\s+)?NumRows\s+:\s+\d+~', '~(\s+)?Height\s+:\s+\d+~', '~(\s+)?Numsites\s+:\s+\d+~'];

    if (!$handle) {
        echo "Problem with openning the file from: $fullPath<br/>";
        return;
    }

    while (($str = fgets($handle, 4096)) !== false) {
        if (preg_match($patterns[0], $str, $match)) {

            preg_match('~\d+~', $match[0], $new_match);
            $n_rows = $new_match[0];
        } else if (preg_match($patterns[1], $str, $match)) {

            preg_match('~\d+~', $match[0], $new_match);
            $rows_height = $new_match[0];
        } else if (preg_match($patterns[2], $str, $match)) {

            preg_match('~\d+~', $match[0], $new_match);
            $boardWidth = $new_match[0];
            break; // If we get the first Numsites we dont need anything else because its just gets repareted afterwards.
        }
    }
    return ['n_rows' => $n_rows, 'rows_height' => $rows_height, 'width' => $boardWidth];
}
function assignNetsToNodes($nodes, $fullPath){
    $pattern = '~NetDegree~';
    $handle = fopen($fullPath, "r");

    if (!$handle) {
        echo "Problem with openning the file from: $fullPath<br/>";
        return;
    }

    // To skip unwanted lines
    while (($str = fgets($handle, 4096)) !== false)
        if (preg_match('~NumPins~', $str)) break;


    $net_count = 0;
    while (($str = fgets($handle, 4096)) !== false) {

        // If you find a new 'NetDegree' make a new net
        // else give to the nodes the current net;
        if (preg_match($pattern, $str)) {
            $net_count++;
        } else {
            preg_match('~[ap]\d+~', $str, $nodeName);
            $nodes[$nodeName[0]]->setNet($net_count);
        }
    }
    if (!feof($handle))  echo "Error: unexpected fgets() fail <br/>";
    // print_r($nodes);
    fclose($handle);
    return ['nodes' => $nodes, 'net_count' => $net_count];
}
function getNodePositions($nodes, $fullPath){
    $pattern = '~\s{0,}\w\d+\s+\d+\s+\d+~';
    $handle = fopen($fullPath, "r");

    if (!$handle) {
        echo "Problem with openning the file from: $fullPath<br/>";
        return;
    }

    while (($str = fgets($handle, 4096)) !== false) {

        if (preg_match($pattern, $str, $match)) {
            $tempNode = formatLine($match[0]);
            $nodes[$tempNode[0]]->setCordinates($tempNode[1], $tempNode[2]);
        }
    }
    if (!feof($handle))  echo "Error: unexpected fgets() fail <br/>";
    fclose($handle);
    return $nodes;
}
function generateNodes($fullPath){

    $pattern = '~^\s{0,}[a,p]\d+\s+\d+\s+\d+(\s{0,} | terminal)$~';
    $nodes = array();
    $handle = fopen($fullPath, "r");
    if (!$handle) {
        echo "Problem with openning the file from: $fullPath<br/>";
        return;
    }
    while (($str = fgets($handle, 4096)) !== false) {

        if (preg_match($pattern, $str, $match)) {
            $tempNode = formatLine($match[0]);
            $nodes[$tempNode[0]] = new Node($tempNode[0], $tempNode[1], $tempNode[2]); // Saving with the name
        }
    }
    if (!feof($handle))  echo "Error: unexpected fgets() fail <br/>";

    fclose($handle);
    return $nodes;
}
function formatLine($match){

    // Seperate the line
    $token = strtok($match, ' ');
    $temp = 0;
    while ($token !== false) {
        $node[$temp] = $token;
        // echo $token .'<br/>';
        $token = strtok(' ');
        $temp++;
    }
    return $node;
}

?>
