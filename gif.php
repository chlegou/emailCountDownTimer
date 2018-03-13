<?php

    include 'GIFEncoder.class.php';
    include 'php52-fix.php';


    // __DIR__ . DIRECTORY_SEPARATOR/gif.php?time=2018-03-20+00:00:01&background=00ee00&timezone=Africa/Tunis
    // demoLink: http://localhost:999/demos/demo-emailCountdownTimer/gif.php?time=2018-03-20+00:00:01&background=00ee00&timezone=Africa/Tunis


    // if no time was set, we put a default expired one
    $time = (isset($_GET['time']) && !empty($_GET['time'])) ? $_GET['time'] : '2000-03-20+00:00:01';
    // default white background
    $background = (isset($_GET['background']) && !empty($_GET['background']) && (ctype_xdigit($_GET['background']) && strlen($_GET['background'])==6)) ? $_GET['background'] : 'ffffff';
    list($r, $g, $b) = array_map('hexdec', str_split($background, 2));

    // default UTC timezone
    $timezone = (isset($_GET['timezone']) && !empty($_GET['timezone'])) ? $_GET['timezone'] : 'UTC';
    // http://php.net/manual/en/timezones.php
    // if the given timezone isn't correct, we set it to default UTC timezone
	if(!date_default_timezone_set($timezone)){
        date_default_timezone_set('UTC');
    }



    $imagePath = 'images/countdown-background-v2.png';

    //default height and width in pixel
    $imageDimensions = getimagesize($imagePath);
    /*$width = (isset($_GET['width']) && !empty($_GET['width']) && ($_GET['width'] < $imageDimensions[0])) ? $_GET['width'] : $imageDimensions[0];
    $height = (isset($_GET['height']) && !empty($_GET['height']) && ($_GET['height'] < $imageDimensions[1])) ? $_GET['height'] : $imageDimensions[1];*/
    $width = (isset($_GET['width']) && !empty($_GET['width']) ) ? $_GET['width'] : $imageDimensions[0];
    $height = (isset($_GET['height']) && !empty($_GET['height'])) ? $_GET['height'] : $imageDimensions[1];


    $future_date = new DateTime(date('r',strtotime($time)));
	$time_now = time();
	$now = new DateTime(date('r', $time_now));
	$frames = array();	
	$delays = array();

	// this function will create a centered copy from the source file image.
    function copyTransparent($src, $newWidth, $newHeight, $red, $green, $blue)
    {
        $dimensions = getimagesize($src);
        $x = $dimensions[0];
        $y = $dimensions[1];
//        $im = imagecreatetruecolor( ($newWidth <= $x) ? $newWidth : $x , ($newHeight <= $y) ? $newHeight : $y);
        $im = imagecreatetruecolor( $newWidth, $newHeight );
        $src_ = imagecreatefrompng($src);
        // Prepare alpha channel for transparent background
        // alpha must be zero!!!!
        $alpha_channel = imagecolorallocatealpha($im, $red, $green, $blue, 0);
        imagecolortransparent($im, $alpha_channel);
        // Fill image
        imagefill($im, 0, 0, $alpha_channel);
        // Copy from other
        imagecopy($im,$src_, ($x - $newWidth < 0) ? ($newWidth- $x) / 2 : 0, ($y - $newHeight < 0) ? ($newHeight- $y) / 2 : 0, ($x - $newWidth >= 0) ? ($x - $newWidth) / 2 : 0, ($y - $newHeight >= 0) ? ($y - $newHeight) / 2 : 0, $x, $y);
//        imagecopy($im,$src_, 0, 0, 2,  2, $x, $y);
        // Save transparency
        imagesavealpha($im,true);

        return $im;
    /*    // Save PNG
        imagepng($im,$output,9);
        imagedestroy($im);*/
    }



    // Your image link
	$image = copyTransparent($imagePath, $width, $height, $r, $g, $b);


	$delay = 100;// milliseconds

    // when changing fonts, we need to adjust the position!!!!
	$font = array(
		'size' => 30, // Font size, in pts usually.
		'angle' => 0, // Angle of the text
        // The larger the number the further the distance from the left hand side, 0 to align to the left.
        'x-offset' => array(// +73 offset
            'days' => 174 - ($imageDimensions[0] - $width) / 2 ,
            'hours' => 247 - ($imageDimensions[0] - $width) / 2 ,
            'minutes' => 320 - ($imageDimensions[0] - $width) / 2 ,
            'seconds' => 394 - ($imageDimensions[0] - $width) / 2 ,
        ),
        'y-offset' => 150 - ($imageDimensions[1] - $height) / 2 , // The vertical alignment, trial and error between 20 and 60.
		'file' => 'fonts/BEBASNEUE-REGULAR.ttf', // Font path
		'color' => imagecolorallocate($image, 255, 255, 255), // RGB Colour of the text
	);
	for($i = 0; $i <= 60; $i++){
		
		$interval = date_diff($future_date, $now);
		
		if($future_date < $now){
			// Open the first source image and add the text.
			$image = copyTransparent($imagePath, $width, $height, $r, $g, $b);


			// days
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['days'] , $font['y-offset'] , $font['color'] , $font['file'], '00' );
            // hours
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['hours'] , $font['y-offset'] , $font['color'] , $font['file'], '00' );
            // minutes
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['minutes'] , $font['y-offset'] , $font['color'] , $font['file'], '00' );
            // seconds
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['seconds'] , $font['y-offset'] , $font['color'] , $font['file'], '00' );

            ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 1;
			ob_end_clean();
			break;
		} else {
			// Open the first source image and add the text.
			$image = copyTransparent($imagePath, $width, $height, $r, $g, $b);

			$days = $interval->format('%a');
			//add zero padding for days
            if(strlen($days) == 1){
                $days = '0'.$days;
            }
            // days
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['days'] , $font['y-offset'] , $font['color'] , $font['file'], $days );
            // hours
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['hours'] , $font['y-offset'] , $font['color'] , $font['file'], $interval->format('%H') );
            // minutes
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['minutes'] , $font['y-offset'] , $font['color'] , $font['file'], $interval->format('%I') );
            // seconds
            imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset']['seconds'] , $font['y-offset'] , $font['color'] , $font['file'], $interval->format('%S') );




            ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 0;
			ob_end_clean();
		}

		$now->modify('+1 second');
	}

	// expire this image instantly
	header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
	$gif = new AnimatedGif($frames,$delays,$loops);
	$gif->display();
