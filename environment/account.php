<?php
    session_start();
    if(!(isset($_SESSION['user']))){
        echo '<meta http-equiv="refresh" content="0;url=index.php">';
        die();
    }
?>
<!DOCTYPE html>
<html>
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


    <!--- main nav--->
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
    <script type="text/javascript" src="javascript/account.js"></script>
        <!-- Still needs to be fixed, result wanted: https://www.w3schools.com/howto/howto_js_tabs.asp -->

        <!-- Tab links -->
        <div class="tab">
            <button class="tablinks" style="font-size:25px;" onclick="openTab(event, 'findcars')">Find Cars</button>
            <button class="tablinks" style="font-size:25px;" onclick="openTab(event, 'myaccount')">My Account</button>
            <button class="tablinks" style="font-size:25px;" onclick="openTab(event, 'mybookings')">My Bookings</button>
        </div>

        <!-- Tab content -->
        
        <div id="findcars" class="tabcontent">
            <h3 style="font-size:30px; padding-left: 20px">Book Available Cars</h3>
            <p>
                <!------This is the section for the Google Maps----------->
                <form method="POST" action="booking.php" >
                    
                    <input type="hidden" name="carToBook" id="carToBook"/>
                    <input type="hidden" name="carType" id="carType"/>
                                      
                    <div id="map"></div>
            
                    <script>
                        function initMap() {
                            var rmit = { lat: -37.807, lng: 144.963 };
                            var map = new google.maps.Map(document.getElementById('map'), {
                                zoom: 15,
                                center: rmit
                            });
                            loadMapData(map);
                        }
                    </script>
                    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA8OtHsqDm-Xi4aOMcTh90M58PUY0zlJc8&callback=initMap">
                    </script>
                    <!-----The script to interface with DB------->
                    <script src="https://sdk.amazonaws.com/js/aws-sdk-2.7.16.min.js"></script>
                    <script>
                        
                        
                        function loadMapData(map) {
                            document.getElementById('textarea').innerHTML += "getting user|"
                            AWS.config.update({
                                region: "us-west-2",
                                endpoint: 'dynamodb.us-west-2.amazonaws.com',
                                // accessKeyId default can be used while using the downloadable version of DynamoDB. 
                                // For security reasons, do not store AWS Credentials in your files. Use Amazon Cognito instead.
                                accessKeyId: "AKIAIAGHSUW7LN42DWBQ",
                                // secretAccessKey default can be used while using the downloadable version of DynamoDB. 
                                // For security reasons, do not store AWS Credentials in your files. Use Amazon Cognito instead.
                                // Temp measure to build test functionality
                                secretAccessKey: "QTTxPX3LuSjjSVFcM5PVSasdQC6oNj2g+BK3Demr"
                            });
                            var docClient = new AWS.DynamoDB.DocumentClient();
            
                            document.getElementById('textarea').innerHTML += "Scanning table." + "\n";
            
                            var params = {
                                TableName: "Cars",
                                ProjectionExpression: "#id, #lat, #lng, #make, #model, #type, #clr",
                                ExpressionAttributeNames: {
                                    "#id": "CarID",
                                    "#lat": "Lat",
                                    "#lng": "Long",
                                    "#make": "Make",
                                    "#model": "Model",
                                    "#type": "Type",
                                    "#clr": "Colour",
                                }
                            };
            
                            docClient.scan(params, onScan);
            
                            function onScan(err, data) {
                                if (err) {
                                    document.getElementById('textarea').innerHTML += "Unable to scan the table: " + "\n" + JSON.stringify(err, undefined, 2);
                                }
                                else {
                                    // Print
                                    document.getElementById('textarea').innerHTML += "Scan succeeded. " + "\n";
                                    data.Items.forEach(function(car) {
                                        document.getElementById('textarea').innerHTML += car.CarID + ": Lat " + car.Lat + " - Long " + car.Long + "\n";
                                        var markerTemp = { lat: car.Lat, lng: car.Long };
                                        var marker = new google.maps.Marker({
                                            position: markerTemp,
                                            map: map
                                        });
                                    
                                        var tempCar = car.CarID;
                                        // create info window
                                        var contentString = '<div id="content">'+
                                        '<div id="siteNotice">'+
                                        '</div>'+
                                        '<h1 id="firstHeading" class="firstHeading">'+car.CarID+'</h1>'+
                                        '<div id="bodyContent">'+
                                        '<p>Car Details<br/>'+
                                        'Make: '+ car.Make+'<br/>'+
                                        'Model: '+car.Model+'<br/>'+
                                        'Colour: '+car.Colour+'<br/>'+
                                        'Type: '+car.Type+
                                        '</p>'+
                                        '<p><button type="submit" onclick="setCarValue(\''+tempCar+'\',\''+car.Type+'\')">Book car</button></p>'+
                                        '</div>'+
                                        '</div>';
                                        
                                        var infowindow = new google.maps.InfoWindow({
                                            content: contentString
                                        });
                                        
                                        marker.addListener('click', function() {
                                            infowindow.open(map, marker);
                                        });
                                    });
                                    
            
                                    // Continue scanning if we have more movies (per scan 1MB limitation)
                                    
                                    if (params.ExclusiveStartKey != data.LastEvaluatedKey) {
                                        params.ExclusiveStartKey = data.LastEvaluatedKey;
                                        docClient.scan(params, onScan);
                                    }
                                }
                                
                            }
                            
                        }
                    </script>    
                    <script type="text/javascript">
                        function setCarValue(id,type){
                            document.getElementById("carToBook").value = id;
                            document.getElementById("carType").value = type;
                        }
                    </script>
            
                    <textarea hidden readonly id="textarea"></textarea>
                                      
                </form>
        <!-------end section------------->
        </p>
        </div>
        <div id="myaccount" class="tabcontent">
            <h3 style="font-size:30px; padding-left: 20px">Account Details</h3>
            <p style="font-size:25px; text-align:left;">
                <br> Email: <?php echo $_SESSION['user'];?>
                <br> Name: <?php echo $_SESSION['userName'];?>
                
                <br/><br/>
                <a href="forgotpassword.php" style="color:blue">RESET PASSWORD HERE</a>
                <br/><br/>
                
            </p>
            <div>
                <form action="updateUserName.php" method="POST">
                    <br/> Add/Update User Name <br/>
                        Enter Name: <input type="text" id="updateName" name="updateName" required>
                        <button type="submit">Update</button>
    
                </form>
            </div>
        </div>
        <div id="mybookings" class="tabcontent">
            <h3 style="font-size:30px; padding-left: 20px">Booking History</h3>
            <p style="font-size:25px;">
                <?php
                
                    require 'aws/aws.phar';
                    
                    $user = $_SESSION['user'];
                    
                    
                    date_default_timezone_set('UTC');
                    
                    use Aws\DynamoDb\Exception\DynamoDbException;
                    use Aws\DynamoDb\Marshaler;
                    
                    $sdk = new Aws\Sdk([
                      //'endpoint'   => 'dynamodb.us-west-2.amazonaws.com',
                      'region'   => 'us-west-2',
                      'version'  => 'latest',
                      'credentials' => [
                        'key'    => 'AKIAIAGHSUW7LN42DWBQ',
                        'secret' => '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',
                    ],
                    ]);

                      
                    $dynamodb = $sdk->createDynamoDb();
                    $marshaler = new Marshaler();
                    
                    $tableName = 'Bookings';
                    
                    $eav = $marshaler->marshalJson('
                        {
                            ":usr": "'.$user.'"
                        }
                    ');
                    
                    $params = [
                        'TableName' => $tableName,
                        'ProjectionExpression' => 'carID, #dtTime, #dt, StartTime, EndTime, #usr',
                        'FilterExpression' => '#usr = :usr',
                        'ExpressionAttributeNames'=> [ '#dt' => 'Date' , '#usr' => 'User', '#dtTime' => 'dateTime'],
                        'ExpressionAttributeValues'=> $eav
                    ];
                    
                    try {
                        $result = $dynamodb->scan($params);
                        
                        while(true){
                            foreach($result['Items'] as $i){
                                $book = $marshaler->unmarshalItem($i);
                                
                                echo 'Car ID:'.$book['carID'].'<br/>';
                                echo 'Date: '.$book['Date'].'<br/>';
                                echo 'Start:'.$book['StartTime'].'<br/>';
                                echo 'End: '.$book['EndTime'].'<br/>';
                                echo '---------<br/>';
                            }
                            
                            if (isset($result['LastEvaluatedKey'])) {
                                $params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
                            } else {
                                break;
                            }
                        }
                        
                    }
                    catch (DynamoDbException $e) {
                        echo "Unable to query:\n";
                        echo $e->getMessage() . "\n";
                    }
                
                ?>
                
            </p>
        </div>

    </main>
    <!-- Button to open the modal -->

    <?php include('register.php'); ?>
    
    <!-- The Modal (contains the Sign Up form)
    <div id="id01" class="modal">
        <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">X</span>
        <form class="modal-content" action="/action_page.php">
            <div class="container">
                <h1 align='center'>Registration</h1>
                <p align='center'>Please fill in this form to create an account.</p>
                <hr>
                <label for="email"><b>Email</b></label>
                <input type="email" placeholder="Enter Email" name="email" required>

                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>

                <label for="psw-repeat"><b>Repeat Password</b></label>
                <input type="password" placeholder="Repeat Password" name="psw-repeat" required>

                <label>
        <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Remember me
                </label>
                <hr>

                <p style="font-size:12px">By creating an account you agree to our <a href="#" style="color:dodgerblue">Terms & Privacy</a>.</p>

                <div class="clearfix">
                    <button type="submit" class="signup">Sign Up</button>
                    <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    -->
    
    
    <?php include('login.php'); ?>
    
    <!-- The Modal (contains the Sign In Form
    <div id="id02" class="modal">
        <span onclick="document.getElementById('id02').style.display='none'" class="close" title="Close Modal">X</span>
        <form class="modal-content" action="/action_page.php">
            <div class="container">
                <h1 align='center'>Sign In</h1>
                <hr>
                <label for="email"><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>

                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>

                <label>
        <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Remember me
                </label>

                <p align="center" style="font-size:15px">Forgot<a href="forgotpassword.html" style="color:dodgerblue"> Password?</a></p>
                <hr>
                <div class="clearfix">
                    <button type="submit" class="signin">Submit</button>
                    <button type="button" onclick="document.getElementById('id02').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    -->


</body>


<div id="footer" class="main_footer_navigation">
    <?php include 'footermenu.php'; ?>
</div>


</html>