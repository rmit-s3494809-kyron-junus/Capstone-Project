<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RideOn Main Homepage">
    <meta name="keywords" content="Homepage">

    <title>RideOn Car Share</title>
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <div>
        <div id="logo-banner">
            <a href="index.php">
        <img id="logo" src="img/Logo v1.png" alt="RideOn Logo">
        </a>
        </div>
    </div>
    
<!-- Main Navigation -->

    <div id="header" class="main_navigation">
        <?php include 'navmenu.php'; ?>
        
        <div id="btnreg" class="btn">
                    <button class="bttn-material-flat bttn-md bttn-primary" onclick="document.getElementById('id01').style.display='block'">Register</button>
                </div>
                <div id="btnsign">
                    <button class="bttn-material-flat bttn-md bttn-primary" onclick="document.getElementById('id02').style.display='block'">Sign In</button>
                </div>
    </div>

    
    <!--- end of main nav--->

    <!-- Place Picture of Cars as Header Banner -->
    <div id="cars-banner">
        <img id="cars" src="img/cars_banners.png" alt="RideOn Logo">
    </div>
</head>

<body>

    <main>

<!-- put 'pay now' button to confirmation.php, to exchange the 'confirm' button
after user press pay now, it will redirect to their sandbox personal, and make the payment,
if payment successful, it should direct them to 'makeBooking.php' where booking is successfully made -->

    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
        
        <!--PayPayl Variables -->
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="5K5FFLZJWB546">
        <input type="hidden" name="business_name" value="RideOn Car Sharing">
        <input type="hidden" name="upload" value="1">
        <input type="hidden" name="quantity" value='<? php echo $totalTime; ?>'/>
        <input type="hidden" name="item_name" value='<? php echo $carName; ?>'/>
        <input type="hidden" name="amount" value='<? php echo $totalCost; ?>'>
        
        <input type="hidden" name="currency_code" value="AUD">
        <input type="image" src="https://www.sandbox.paypal.com/en_AU/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.sandbox.paypal.com/en_AU/i/scr/pixel.gif" width="1" height="1">
    </form>

        <!--end section-->




    </main>

</body>

<!-- Button to open the modal -->

    
        <?php include('register.php'); ?>

<!-- The Modal (contains the Sign Up form) -->

<!-- The Modal (contains the Sign In Form -->

    <?php include('login.php'); ?>


<div id="footer" class="main_footer_navigation">
    <?php include('footermenu.php'); ?>
</div>

</html>
