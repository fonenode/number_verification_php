<?php
session_start();
if ($_POST) {
    $pin = (int) $_POST['pin'];
    if ($_SESSION['pin'] == $pin) {
        $success = 'Number verification successful. Thank you.';
    }
    else {
        $error = 'Incorrect PIN. Please try again';
    }
}
?>
<!DOCTYPE HTML> 
<html lang=en>
<head>
<title>Verify PIN</title>
</head>
<body>
    <?php
    if ($success) {
        echo '<p class="success">'.$success.'</p>';
    }
    else {
        if ($error)
            echo '<p class="error">'.$error.'</p>';
        ?>
        <form method="post">
            <p>Enter PIN:</p>
            <p><input type="text" name="pin" /></p>
            <p><button type="submit">Confirm</button></p>
        </form>
        <?php
    }
    ?>
</body>
</html>