<?php
include 'checkingFiles.php';



$failed   = array();
$uploaded = array();
$allowed  = array('aux', 'nets', 'nodes', 'pl', 'scl', 'wts');

if (!file_exists('./uploads')) {
    mkdir('./uploads', 0777, true);
}
$upload_folder = 'uploads/';


if(!empty($_FILES['files']['name'][0])){
    $files = $_FILES['files'];


    foreach($files['name'] as $position => $file_name){
        $file_tmp   = $files['tmp_name'][$position];
        $file_size  = $files['size'][$position];
        $file_error = $files['error'][$position];
        
        $file_ext   = explode('.', $file_name);
        $file_ext   = strtolower(end($file_ext));
        if(in_array($file_ext, $allowed)){
            if($file_error === 0){

                if($file_size <= 0){
                    $failed[$position] = "[$file_name] is empty {$file_error}";
                } else {
                    $files_name_new =  explode('.', $file_name)[0] . '.' . $file_ext;
                    $file_destination = $upload_folder . $files_name_new;

                    if(move_uploaded_file($file_tmp, $file_destination)){
                        $uploaded[$position] = $file_destination;
                    }else{
                        $failed[$position] = "[{$file_name}] failed to upload";
                    }
                }
            } else {
                $failed[$position] = "[$file_name] errored with code {$file_error}";
            }
        } else {
            $failed[$position] = "[{$file_name}] file extension '{$file_ext}' is not allowed";
        }

        //echo '<pre>', print_r($file_ext), '</pre>';
    }


}

// We need all the 6 files to be uploaded
if (count($uploaded) === 6) {
    
    //generateData(); // From checkingFiles.php
   // var_dump($uploaded);
    header('Location: index.php?id=-9185');
}else{
    //$data = array();
}

foreach($failed as $fail){
    echo "<p>UPLOAD ERROR: $fail</p><br>";
}
$error_msg = "<p>Please Upload <strong>ALL</strong> 6 files needed correctly</p>";
include 'config/wrong_input.php';
?>
