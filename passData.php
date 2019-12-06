<?php

include 'checkingFiles.php';


if (isset($_GET['id'])) {
    deleteDir('temp_uploads');
    $id = (int) htmlspecialchars($_GET['id']);
    if ($id === -9185) 
        generateData('uploads'); // From checkingFiles.php 
    else{
        downloadFiles($id);
        generateData('temp_uploads');
    }
}

sendData();

function deleteDir($dir) {
    if(file_exists($dir)){
        $files = scandir("./$dir");
    
        foreach($files as $file){
            if($file === '.' || $file === '..') continue;
            unlink("./$dir/" . $file);
        }
        rmdir($dir);
    }
}

function downloadFiles($id){
    include 'config/db_connect.php';


    // fetch file to download from database
    $sql = "SELECT * FROM files WHERE designID = $id";
    $result = mysqli_query($connect, $sql);

    $files = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);

    if (!file_exists('./temp_uploads')) {
        mkdir('./temp_uploads', 0777, true);
    }

    foreach($files as $file){

        $file_path = 'temp_uploads/' . $file['name'] .'.'.$file['type'];
        $file_d   = fopen($file_path, "w") OR die("Unable to open file: $file_path");

        fwrite($file_d, $file['data']);
        fclose($file_d);
    }
    mysqli_close($connect);
}

    // if(isset($_GET['id'])){
    
    //     $id = (int) htmlspecialchars($_GET['id']);
    
    //     if($id === -9185){
    //         generateData(); // From checkingFiles.php
    //     }else{  
    //         include 'config/db_connect.php';
    //         receiveDesign($connect, $id);
    //         receiveBoard($connect, $id);
    //         receiveCells($connect, $id);
    //         mysqli_close($connect);
    //     }
    // } 

function receiveDesign($connect, $id ){
    global $net_count;
    
    // Make sql for cells
    $sql = "SELECT * FROM design WHERE design.ID = $id";

    // get the query result
    $result = mysqli_query($connect, $sql);

    // fetch result in array format
    $design_db = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    // print_r($design_db);
    $net_count = $design_db['net_count'];
}

function receiveBoard($connect, $id){
    global $board;

    // Make sql for cells
    $sql = "SELECT width, height, n_rows, rows_height FROM board WHERE board.designID = $id";

    // get the query result
    $result = mysqli_query($connect, $sql);

    // fetch result in array format
    $board_db = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    
    $board = new Board($board_db['width'], $board_db['rows_height'], $board_db['n_rows']);
}
function receiveCells($connect, $id){
    global $nodes;
    $empty_nets_query  = false;
    $empty_nodes_query = false;
    $low_limit  = 0;
    $high_limit = 0;

    //TODO: THIS IS WRONG!!
    while(!$empty_nodes_query && !$empty_nets_query){
        $low_limit   = $high_limit;
        $high_limit += 3000;
        
        
        // Make sql for cells
        $sql = "SELECT * FROM cell WHERE cell.designID = $id LIMIT $low_limit, $high_limit";

        // get the query result
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) == 0) $empty_nodes_query = true;

        // fetch result in array format
        $nodes_db = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);

        // Make sql for nets
        $sql = "SELECT cellName, netNum FROM cell_nets WHERE cell_nets.designID = $id LIMIT $low_limit, $high_limit";

        // get the query result
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) == 0) $empty_nets_query = true;

        // fetch result in array format
        $nets_db = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        //print_r($nets_db);

        foreach ($nodes_db as $node_db) {
            $temp_node = new Node($node_db['cellName'], $node_db['width'], $node_db['height']);
            $temp_node->setCordinates($node_db['x'], $node_db['y']);

            // $temp_arr = array();
            foreach ($nets_db as $net_db) {
                if ($net_db['cellName'] === $temp_node->getName())
                    $temp_node->setNet($net_db['netNum']);
                //array_push($temp_arr, );
            }

            // $temp_node->setNet($temp_arr);
            array_push($nodes, $temp_node);
        }
    }
    

    
}
function sendData(){
    global $board;
    global $nodes;
    global $net_count;
    $send_data = array();
    $arr = (array) $board;
    array_push($send_data, $arr); // The first el of the arr is the board data
    array_push($send_data, $net_count); // Second the number of nets
    
    foreach ($nodes as $node) {
        $arr = (array) $node;
        array_push($send_data, $arr);
    }
    // echo '<pre>';
    // print_r($send_data);
    // echo '</pre>';

    if(!empty($send_data))
        echo json_encode($send_data);

}


?>