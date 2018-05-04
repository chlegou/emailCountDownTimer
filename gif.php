<?php

include 'CountdownTimerGif.class.php';


// if no time was set, we put a default expired one
$time = (isset($_GET['time']) && !empty($_GET['time'])) ? $_GET['time'] : '2000-03-20+00:00:01';
// default white backgroundColor
$backgroundColor = (isset($_GET['backgroundColor']) && !empty($_GET['backgroundColor']) && (ctype_xdigit($_GET['backgroundColor']) && strlen($_GET['backgroundColor'])==6)) ? $_GET['backgroundColor'] : 'ffffff';

// default white counterColor
$counterColor = (isset($_GET['counterColor']) && !empty($_GET['counterColor']) && (ctype_xdigit($_GET['counterColor']) && strlen($_GET['counterColor'])==6)) ? $_GET['counterColor'] : 'ffffff';

// default as counterColor
$counterGlowColor = (isset($_GET['counterGlowColor']) && !empty($_GET['counterGlowColor']) && (ctype_xdigit($_GET['counterGlowColor']) && strlen($_GET['counterGlowColor'])==6)) ? $_GET['counterGlowColor'] : $counterColor;

//default size 0 so it's not set
$counterGlowSize = (isset($_GET['counterGlowSize']) && !empty($_GET['counterGlowSize'])) ? $_GET['counterGlowSize'] : 0;

// default black indicatorsColor
$indicatorsColor = (isset($_GET['indicatorsColor']) && !empty($_GET['indicatorsColor']) && (ctype_xdigit($_GET['indicatorsColor']) && strlen($_GET['indicatorsColor'])==6)) ? $_GET['indicatorsColor'] : '000000';

// default as indicatorsColor
$indicatorsGlowColor = (isset($_GET['indicatorsGlowColor']) && !empty($_GET['indicatorsGlowColor']) && (ctype_xdigit($_GET['indicatorsGlowColor']) && strlen($_GET['indicatorsGlowColor'])==6)) ? $_GET['indicatorsGlowColor'] : $indicatorsColor;

//default size 0 so it's not set
$indicatorsGlowSize = (isset($_GET['indicatorsGlowSize']) && !empty($_GET['indicatorsGlowSize'])) ? $_GET['indicatorsGlowSize'] : 0;



// default UTC timezone
$timezone = (isset($_GET['timezone']) && !empty($_GET['timezone'])) ? $_GET['timezone'] : 'UTC';


$imagePath = 'images/countdown-background-v3.png';

// default height and width in pixel, used to calculate the default width and height
$imageDimensions = getimagesize($imagePath);
$width = (isset($_GET['width']) && !empty($_GET['width']) ) ? $_GET['width'] : $imageDimensions[0];
$height = (isset($_GET['height']) && !empty($_GET['height'])) ? $_GET['height'] : $imageDimensions[1];

// we create the gif, it will return AnimatedGif instance. then we display it
$gif = CountdownTimerGif::create($imagePath, $time, $timezone, $backgroundColor, $counterColor, $counterGlowColor, $counterGlowSize, $indicatorsColor, $indicatorsGlowColor, $indicatorsGlowSize, $width, $height);
$gif->display();
