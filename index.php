<?php
include "configure.php";

$login_button = '';

if (isset($_GET['code'])) {
    $token = $g_client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $g_client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];

        $google_service = new Google_Service_Oauth2($g_client);

        $data = $google_service->userinfo->get();

        if (!empty($data['email'])) {
            $_SESSION['user_email'] = $data['email'];
        }

    }
}

if (isset($_GET['country'])) {
    //Fetching Mathdro.id API - https://github.com/mathdroid/covid-19-api
    $country = $_GET['country'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://covid19.mathdro.id/api/countries/' . $country);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $covidResult = curl_exec($ch);
    
    // var_dump($covidResult);
    
    $covidData = json_decode($covidResult, true);
    
    // var_dump($covidData);
    
    $convertTime = strtotime($covidData['lastUpdate']);
    $updatedTime = date('F j, Y, g:i a', $convertTime);
    
}

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Hello, world!</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01"
                aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <a class="navbar-brand">News.Daily</a>
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only"></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact Us</a>
                    </li>
                </ul>
                <?php
                if (!isset($_SESSION['access_token'])) {
                    $login_button = '<a href="' . $g_client->createAuthUrl() . '" class="btn btn-danger"><i class="fab fa-google"></i> Sign with google</a>';
                }
                if ($login_button == '') {
                    echo 'Hello, ' . $_SESSION['user_email'] . '|';
                    echo '<a href="logout.php" class="btn btn-danger">Logout</a>';
                } else {
                    echo '<div align="center">' . $login_button . '</div>';
                }
                ?>
            </div>
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="container-fluid">
                <h1>Corona Checker</h1>
                    <form method="post" action="covid.inc.php">
                        <div class="form-group">
                            <input type="text" class="form-control" name="country" id="country"
                                    placeholder="Enter Country">
                        </div>
                        <button type="submit" name="check" class="btn btn-danger pull-right">Check</button>
                    </form>
                    <?php
                    if (isset($_GET['country'])) {
                    ?>
                    <div class="card-deck pt-3 pb-5">
                        <div class="card">
                            <div class="card-body">
                            <h4 class="card-title">Confirmed</h4>
                            <p class="card-text"><?php echo number_format($covidData['confirmed']['value']); ?></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                            <h4 class="card-title">Recovered</h4>
                            <p class="card-text"><?php echo number_format($covidData['recovered']['value']); ?></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                            <h4 class="card-title">Deaths</h4>
                            <p class="card-text"><?php echo number_format($covidData['deaths']['value']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                <?php
                $newsUrl = 'http://newsapi.org/v2/top-headlines?country=ca&apiKey=b11b73809bc04508a0698180dad078f6';
                $response = file_get_contents($newsUrl);
                $newsData = json_decode($response);
                ?>
                <h1>Daily News</h1>
                <div class="card-columns">
                    <?php
                    if (isset($_SESSION['user_email'])) {
                        foreach ($newsData->articles as $news) {
                    ?>
                        <div class="card">
                            <img class="card-img-top" src="<?php echo $news->urlToImage ?>"
                                alt="News card thumbnail">
                                
                            <div class="card-body"> 
                                <h3>
                                    <?php echo $news->title ?> 
                                </h3>
                                </br>
                                <a href="<?php echo $news->url ?>" class="card-link btn btn-success">Read More</a>
                                </br>
                                <h6 style="color: grey; font-weight: normal;">Published at: <?php echo $news->publishedAt ?></h6>   
                            </div>
                        </div>
                    <?php
                        }
                    }
                    ?>
                </div>
                <?php
                    if (!isset($_SESSION['user_email'])) {
                        echo '<div class="alert alert-danger"><strong>Oh snap!</strong> 
                        Please login to view the news! </div><a href="' . $g_client->createAuthUrl() . '" class="btn btn-danger"><i class="fab fa-google"></i> Sign with google</a>';
                    }
                ?>
            </div>
        </div>
        </div>
    </main>

    <footer>

    </footer>

    <script src="js/bootstrap.js"></script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
</body>

</html>