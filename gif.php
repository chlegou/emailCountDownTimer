<?php

include 'CountdownTimerGif.class.php';


// if no time was set, we put a default expired one
$time = (isset($_GET['time']) && !empty($_GET['time'])) ? $_GET['time'] : '2000-03-20+00:00:01';
// default white background
$background = (isset($_GET['background']) && !empty($_GET['background']) && (ctype_xdigit($_GET['background']) && strlen($_GET['background'])==6)) ? $_GET['background'] : 'ffffff';

// default UTC timezone
$timezone = (isset($_GET['timezone']) && !empty($_GET['timezone'])) ? $_GET['timezone'] : 'UTC';


$imagePath = 'images/countdown-background-v2.png';

// default height and width in pixel, used to calculate the default width and height
$imageDimensions = getimagesize($imagePath);
$width = (isset($_GET['width']) && !empty($_GET['width']) ) ? $_GET['width'] : $imageDimensions[0];
$height = (isset($_GET['height']) && !empty($_GET['height'])) ? $_GET['height'] : $imageDimensions[1];

// we create the gif, it will return AnimatedGif instance. then we display it
$gif = CountdownTimerGif::create($imagePath, $time, $timezone, $background, $width, $height);
$gif->display();
