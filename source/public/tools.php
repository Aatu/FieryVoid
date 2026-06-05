<?php
include_once 'global.php';
if (!isset($_SESSION["user"]) || $_SESSION["user"] == false) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Fiery Void - About the Game</title>
  <link href="styles/base.css" rel="stylesheet" type="text/css">
  <link href="styles/lobby.css" rel="stylesheet" type="text/css">
  <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">
  <style>
    .faq-panel {
      font-size: 16px;
      line-height: 1.6;
    }
    .faq-panel p {
      margin-bottom: 15px;
    }
    .faq-panel ul {
      margin-bottom: 20px;
    }
    .faq-panel h3 {
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      padding-bottom: 5px;
    }
    .faq-panel li {
      margin-bottom: 8px;
    }
  </style>
</head>
<body style="background: url('./img/maps/24.PurpleArch.jpg') no-repeat center center fixed; background-size: cover;">

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="games.php">Back to Game Lobby</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="faq-panel">
    <h2 id="top" style="margin-top: 5px">DATA ANALYSIS TOOLS</h2>

    <h3 id="contents" style="margin-top: 25px">PURPOSE</h3>
    <p>Add Fred's text here</p>

    <h3 id="contents" style="margin-top: 25px">TABLE OF CONTENTS</h3>

    <ul class = index-list>
        <li><a href="#unit-analysis">Unit Analysis</a>
            <ul class="sub-list">
                <li><a href="#fighter-analysis">Fighters</a></li>
                <li><a href="#ship-analysis">Ships</a></li>
            </ul>      
		</li>
        <li><a href="#point-calculator">Unit Point Calculater - Derived from Hiflyte's System</a></li>
        <!-- Add more sections here -->
    </ul>

    <h3 id="unit-analysis" style="margin-top: 25px">UNIT ANALYSIS</h3>
    <p>Enter details.</p>

    <h4 id="fighter-analysis">Fighter Analysis:</h4>
    <p>Enter details on fighter analysis.</p>

    <h4 id="ship-analysis">Ship Analysis:</h4>
    <p>Enter details on ship analysis.</p>

    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h3 id="point-calculator" style="margin-top: 25px">UNIT POINT CALCULATOR</h3>
    <p>Enter details on unit point calculator.</p>

	<!-- Add link to the calculator
	<a href="https://www.youtube.com/playlist?list=PLTGKagm5KkMxB8oKBiIUeoBQTRYz2z0-3" target="_blank" rel="noopener noreferrer">Video Tutorials</a> | 
	-->
    <a class="back-to-top" href="#top">↩ Back to Top</a>


  </section>
</main>

<footer class="site-disclaimer">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars. 
It is not affiliated with, endorsed by, or sponsored by any official rights holders. 
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>

</body>
</html>