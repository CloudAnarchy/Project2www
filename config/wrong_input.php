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
        }

        a{
            /* style: none; */
            text-decoration: none;
            color: white;
            position: center;
        }
        body {
            background: rgb(22, 24, 25);
            background: radial-gradient(circle, rgba(22, 24, 25, 1) 0%, rgba(5, 6, 8, 1) 100%);
        }
    </style>
</head>

<body>
    <div>
        <?php echo $error_msg ?>
        <a href="index.php">Try again.</a>

    </div>
</body>

</html>