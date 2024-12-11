<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <header class="header">
        <div class="container">

            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="#">Jobs</a></li>
                    <li><a href="aboutus.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                    
                </div>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h2>Your Dream Job is Waiting</h2>
            <form class="search-bar">
                <div class="input-group">
                    <label for="search-job">Search Job</label>
                    <input type="text" id="search-job" placeholder="Search Keyword">
                </div>
                <div class="input-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" placeholder="Select Location">
                </div>
                <button type="submit" class="search-btn">Search</button>
            </form>
            <div class="most-searches">
                <h4>Most Searches</h4>
                <ul>
                    <li><a href="#">Automotive</a></li>
                    <li><a href="#">Education</a></li>
                    <li><a href="#">Health and Care</a></li>
                    <li><a href="#">Engineering</a></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="categories">
        <h3>Latest Job Listing</h3>
      
    </section>
</body>
</html>