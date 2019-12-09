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
$home_link = "http://$_SERVER[HTTP_HOST]/project2www/index.php";

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
            /* style: none; */
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

        .arrow {
            margin: 100px 0 0 50px;
            width: 80px;
            height: 80px;
            fill: rgb(28, 28, 28);

            transition-duration: 500ms;
        }

        .arrow:hover {
            fill: rgb(177, 177, 177);
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
    <a href="<?php echo $home_link ?>">
        <svg class="arrow" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 492 492" style="enable-background:new 0 0 492 492;" xml:space="preserve">
            <path d="M464.344,207.418l0.768,0.168H135.888l103.496-103.724c5.068-5.064,7.848-11.924,7.848-19.124
                    c0-7.2-2.78-14.012-7.848-19.088L223.28,49.538c-5.064-5.064-11.812-7.864-19.008-7.864c-7.2,0-13.952,2.78-19.016,7.844
                    L7.844,226.914C2.76,231.998-0.02,238.77,0,245.974c-0.02,7.244,2.76,14.02,7.844,19.096l177.412,177.412
                    c5.064,5.06,11.812,7.844,19.016,7.844c7.196,0,13.944-2.788,19.008-7.844l16.104-16.112c5.068-5.056,7.848-11.808,7.848-19.008
                    c0-7.196-2.78-13.592-7.848-18.652L134.72,284.406h329.992c14.828,0,27.288-12.78,27.288-27.6v-22.788
                    C492,219.198,479.172,207.418,464.344,207.418z" />
        </svg>

    </a>

    <!-- <p><?php print_r($designs); ?></p> -->
</body>

</html>