<?php

    include 'GIFEncoder.class.php';
    include 'php52-fix.php';


    // __DIR__ . DIRECTORY_SEPARATOR/gif.php?time=2018-03-20+00:00:01&background=00ee00&timezone=Africa/Tunis
    // demoLink: http://localhost:999/demos/demo-emailCountdownTimer/gif.php?time=2018-03-20+00:00:01&background=00ee00&timezone=Africa/Tunis


    $time = (isset($_GET['time']) && !empty($_GET['time'])) ? $_GET['time'] : '2000-03-20+00:00:01';
    $background = (isset($_GET['background']) && !empty($_GET['background']) && (ctype_xdigit($_GET['background']) && strlen($_GET['background'])==6)) ? $_GET['background'] : 'ffffff';
    list($r, $g, $b) = array_map('hexdec', str_split($background, 2));

    $timezone = (isset($_GET['timezone']) && !empty($_GET['timezone'])) ? $_GET['timezone'] : 'UTC';
    // http://php.net/manual/en/timezones.php
    // if the given timezone isn't correct, we set it to default UTC timezone
	if(!date_default_timezone_set($timezone)){
        date_default_timezone_set('UTC');
    }


	$future_date = new DateTime(date('r',strtotime($time)));
	$time_now = time();
	$now = new DateTime(date('r', $time_now));
	$frames = array();	
	$delays = array();

	// this function will create a copy from the source file image.
    function copyTransparent($src, $red, $green, $blue)
    {
        $dimensions = getimagesize($src);
        $x = $dimensions[0];
        $y = $dimensions[1];
        $im = imagecreatetruecolor($x,$y);
        $src_ = imagecreatefrompng($src);
        // Prepare alpha channel for transparent background
        // alpha must be zero!!!!
        $alpha_channel = imagecolorallocatealpha($im, $red, $green, $blue, 0);
        imagecolortransparent($im, $alpha_channel);
        // Fill image
        imagefill($im, 0, 0, $alpha_channel);
        // Copy from other
        imagecopy($im,$src_, 0, 0, 0, 0, $x, $y);
        // Save transparency
        imagesavealpha($im,true);

        return $im;
    /*    // Save PNG
        imagepng($im,$output,9);
        imagedestroy($im);*/
    }

	$imagePath = 'images/countdown-background.png';


    // Your image link
	$image = copyTransparent($imagePath, $r, $g, $b);


	$delay = 100;// milliseconds

    $loops = 0; // loops

    // when changing fonts, we need to adjust the position!!!!
	$font = array(
		'size' => 24, // Font size, in pts usually.
		'angle' => 0, // Angle of the text
		'x-offset' => 172, // The larger the number the further the distance from the left hand side, 0 to align to the left.
		'y-offset' => 50, // The vertical alignment, trial and error between 20 and 60.
		'file' => __DIR__ . DIRECTORY_SEPARATOR . 'VarelaRound-Regular.ttf', // Font path
		'color' => imagecolorallocate($image, 255, 255, 255), // RGB Colour of the text
	);
	for($i = 0; $i <= 60; $i++){
		
		$interval = date_diff($future_date, $now);
		
		if($future_date < $now){
			// Open the first source image and add the text.
			$image = copyTransparent($imagePath, $r, $g, $b);


			$text = $interval->format('00    00    00    00');
			imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
			ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 1;
			ob_end_clean();
			break;
		} else {
			// Open the first source image and add the text.
			$image = copyTransparent($imagePath, $r, $g, $b);

			$text = $interval->format('%a    %H    %I    %S');
			//add zero padding for days
            if(preg_match('/^[0-9]\ /', $text)){
                $text = '0'.$text;
            }
			imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
			ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 0;
			ob_end_clean();
		}

		$now->modify('+1 second');
	}

	//expire this image instantly
	header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
	$gif = new AnimatedGif($frames,$delays,$loops);
	$gif->display();
