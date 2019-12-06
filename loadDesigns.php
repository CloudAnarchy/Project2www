<?php

include 'config/db_connect.php';


if (isset($_GET['load-design'])) {

    $sql = 'SELECT name AS name, id FROM designs';

    $result = mysqli_query($connect, $sql);

    $designs = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    //if (empty($designs)) echo 'No designs';
}


mysqli_close($connect);
//header("Location: index.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        p {
            font-family: Helvetica, sans-serif;
            color: white;
            /* margin-top: 100px; */
        }

        .designs-container {
            font-weight: 700;
            width: 90%;
            margin: auto;
            display: flex;
            justify-content: space-between;
            justify-items: center;
            padding: 30px 0px;
        }

        a,
        li,
        button {
            style: none;
            text-decoration: none;
            color: white;
            list-style-type: none;
            border: none;
        }

        .design {
            cursor: pointer;
            font-family: Helvetica, Arial, sans-serif;
            background: rgb(22, 24, 25);
            background: radial-gradient(circle, rgba(22, 24, 25, 1) 0%, rgba(5, 6, 8, 1) 100%);
            filter: drop-shadow(0 0 0.75rem crimson);
            padding: 20px;
            margin: 20px;
        }

        body {
            background: rgb(22, 24, 25);
            background: radial-gradient(circle, rgba(22, 24, 25, 1) 0%, rgba(5, 6, 8, 1) 100%);
        }
    </style>
</head>

<body>

    <nav class="designs-container">
        <?php foreach ($designs as $arr) : ?>
            <a href="index.php?id=<?php echo $arr['id']; ?>" class="design">

                <p><?php echo "Name: " . htmlspecialchars($arr['name']);
                        echo "</br>ID: " . htmlspecialchars($arr['id']); ?></p>
            </a>
        <?php endforeach; ?>

    </nav>
    <!-- <p><?php print_r($designs); ?></p> -->
</body>

</html>