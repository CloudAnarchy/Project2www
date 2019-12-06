<?php
include 'checkingFiles.php';

$folderName = 'uploads';
generateData($folderName);
saveDesign($folderName);

function saveDesign($folderName){
    global $nodes;
    // Saving the files.
    if (isset($_POST['save-design']) && !empty($nodes)) {

        include 'config/db_connect.php';
        insertNewDesign($connect);
        $designID = getMAXDesignID($connect);
        insertNewBoard($connect, $designID);
        mysqli_close($connect);


        $dbh = new PDO("mysql:host=localhost;dbname=project2www", "root", "");
        $file_paths = findFiles($folderName);

        foreach($file_paths as $filePath){
            $file_ext = explode('.', $filePath);
            $name     = end(explode('/',$file_ext[0]));
            $type     = strtolower(end($file_ext));
            $data     = file_get_contents($filePath);
            $stmt     = $dbh->prepare("INSERT INTO files VALUES('', ?,?,?,?)");
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $type);
            $stmt->bindParam(3, $data);
            $stmt->bindParam(4, $designID);
            $stmt->execute();
        }

        // Closing the connection with the database.
        $dbh = NULL;
        header('Location: index.php?id=-9185');
    }

    // Saving as seperate data and not as files.
    if(0/*isset($_POST['save-design']) && !empty($nodes)*/){
        include 'config/db_connect.php';

        // Get the last design ID
        insertNewDesign($connect);
        $designID = getMAXDesignID($connect); //rand(10, 1000000000); //

        // echo "<p>" . print_r($designID) . "</p>";

        echo "Design ID: $designID</br></br>";
        
        insertNewBoard($connect, $designID);

        $sql_nets  = "INSERT INTO cell_nets(cellName, designID, netNum) VALUES ";
        $sql_nodes = "INSERT INTO cell(cellName, designID, x, y, width, height, isTerminal) VALUES ";
        // Inserting nodes-cells to the Database.
        $i = 0;
        foreach($nodes as $node){

            // if($i === 20) break;
            // else $i++;
            if($i >= 1500) {
                makeSQL_statements($connect, $sql_nets, $sql_nodes);
                $sql_nets  = "INSERT INTO cell_nets(cellName, designID, netNum) VALUES ";
                $sql_nodes = "INSERT INTO cell(cellName, designID, x, y, width, height, isTerminal) VALUES ";
                
                $i = 0; 
            }

            $i++;

            $name   = $node->getName();
            $cords  = $node->getCordinates();
            $x      = $cords['x'];
            $y      = $cords['y'];
            $nets   = $node->getNets();
            $width  = $node->getWidth();
            $height = $node->getHeight();
            $termin = $node->isTerminal() ? $termin = 1 : $termin = 0;
            
            
            $sql_nodes = $sql_nodes . " ('$name', $designID, $x, $y, $width, $height, $termin) ,";
            
            $sql_nets = insertNetsForNode($sql_nets, $name, $nets, $designID);
            // echo "$i -node: $sql_nodes </br>";
            // echo "$i -net : $sql_nets  </br>";


            //$i++;
            // if(!mysqli_query($connect, $sql_nodes)) 
            //     echo "<p>Query error: " . mysqli_error($connect) . "</p>";
            
        }

        // echo "last-node: $sql_nodes </br>";
        // echo "last-net : $sql_nets  </br>";
        makeSQL_statements($connect, $sql_nets, $sql_nodes);
        
        mysqli_close($connect);
        header("Location: index.php");
    }
    //mysqli_close($connect);
}
function makeSQL_statements($connect, $sql_nets, $sql_nodes){
    $sql_nets  = rtrim($sql_nets, ',');
    $sql_nodes = rtrim($sql_nodes, ',');
    if (!mysqli_query($connect, $sql_nodes) || !mysqli_query($connect, $sql_nets))
        echo "<p>Query error: " . mysqli_error($connect) . "</p>";

}
function insertNewBoard($connect, $designID){
    global $board;

    $width       = $board->getHeight();
    $height      = $board->getWidth();
    $n_rows      = $board->getN_rows();
    $rows_height = $board->getRows_height();

    $sql_board = "INSERT INTO board(designID, width, height, n_rows, rows_height) VALUES ($designID, $width, $height, $n_rows, $rows_height)";

    if (!mysqli_query($connect, $sql_board))
        echo "<p>Query error: " . mysqli_error($connect) . "</p>";
}

function insertNewDesign($connect, $name = 'unknown'){

    global $nodes;
    global $net_count;
    
    $n_nodes = sizeof($nodes);
    $name = generateRandomString(10);

    // $sql_design = "INSERT INTO designs(name) VALUES('$name')";
    $sql_design = "INSERT INTO designs(name, net_count, n_nodes) VALUES ('$name', $net_count, $n_nodes)";

    if (!mysqli_query($connect, $sql_design))
        echo "<p>Query error: " . mysqli_error($connect) . "</p>";
}

function insertNetsForNode($sql_nets, $name, $nets, $designID){
     
    foreach ($nets as $net) {
       $sql_nets = $sql_nets . "('$name', $designID, $net) ,";
    }

    return $sql_nets;
}

function getMAXDesignID($connect){

    $sql = "SELECT MAX(id) AS 'max' FROM designs";
    $result = mysqli_query($connect, $sql);
    if($result)
    
    $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    if(mysqli_num_rows($result) == 0) { 
        $arr = 0;
    }else{
        $arr = $arr[0]['max'];
    }

    mysqli_free_result($result);

    return $arr;
}

function generateRandomString($length = 10){
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}
if(empty($nodes))
    $error_msg = "<p>Have you <strong>uploaded</strong> anything?..</p>";
$error_msg = "";

include 'config/wrong_input.php';
?>
