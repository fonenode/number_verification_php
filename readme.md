##Number verification

###Objective
Provide a simple number verification process by sending a PIN to the number via a voice call using the fonenode api. A demo is available at [demo.fonenode.com/otp](http://demo.fonenode.com/otp)

Here are the steps:

1. Ask user for his/her phone number.
2. Generate a random PIN and send to number via voice call.
3. Ask the user to enter the PIN.

So...

1. Ask user for phone number.  
We simply create a simple form for this. (You may have your own number form field buried in a bigger form.)

        <!DOCTYPE HTML> 
        <html lang=en>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Number verification</title>
        </head>
        <body>
            <form method="post">
                <p>Your mobile number:</p>
                <p><input type="text" name="number" /></p>
                <p><button type="submit">Call</button></p>
            </form>
        </body>
        </html>

2. Generate a random PIN and send to number via voice call.

		<?php
		// We will be storing the pin in session
		session_start();
		require 'lib/fonenode.php';
		$fonenode = new fonenode('__auth_id___', '__auth_key___');

		if ($_POST) {
			$to = $_POST['number'];
			// Remove any non-digit character
			$to = preg_replace('|[^0-9]|', '', $to);
			// Format number to international standard
			if (preg_match('|^0|', $to))
				$to = '234'.substr($to, 1);
				
			// Generate pin - random number between 1000 and 9999
			$_SESSION['pin'] = mt_rand(1000, 9999);
			// Space pad the pin so that it's clear over voice
			// Assuming our pin is 1234, this gives 1. 2. 3. 4.
			$pin = preg_replace('|([0-9])|', '$1. ', $_SESSION['pin']);
			$text = "Your pin is. $pin. Again, your pin is. $pin. Thank you.";
			
			// Now lets call user using fonenode's quick call API
			// http://fonenode.com/docs#calls-quick
			$json = $fonenode->quick_call($to, $text);
			$status = $fonenode->getStatusCode();
			//print_r($json);
			
			if ($status == 200) {
				/*
				// Response should look like
				{"fired": [{
				  "id": "XXXXXX",
				  "number": "23480XXXXXXXX"
				}],
				"errors": []}
				//*/
				if ($json['fired'][0]) {
					// All is well, redirect to page to enter PIN
					header('location:confirm.php');
					exit;
				}
				else {
					// There has been an error firing the call
					$error = $json['error'][0]['error'];
				}
			}
			else {
				// Something has gone wrong
				// Insufficient balance, wrong auth details, internal server error, ...
				// Simply log the details here
				// your_log_function($status, $json['error']);
				$error = "There has been an error confirming the call. Please try again later.";
			}
		}
		?>

		<!DOCTYPE HTML> 
		<html lang=en>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Number verification</title>
		</head>
		<body>
			<?php
			if ($error)
				echo '<p class="error">'.$error.'</p>';
			?>
			<form method="post">
				<p>Your mobile number:</p>
				<p><input type="text" name="number" /></p>
				<p><button type="submit">Call</button></p>
			</form>
		</body>
		</html>

3. Ask the user to enter the PIN and confirm.

        <?php
        session_start();
        if ($_POST) {
            $pin = (int) $_POST['pin'];
            if ($_SESSION['pin'] == $pin) {
                // Number successfully verified
                // do other things here like redirect else where
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
            if ($error)
                echo '<p class="error">'.$error.'</p>';
            ?>
            <form method="post">
                <p>Enter PIN:</p>
                <p><input type="text" name="pin" /></p>
                <p><button type="submit">Confirm</button></p>
            </form>
        </body>
        </html>