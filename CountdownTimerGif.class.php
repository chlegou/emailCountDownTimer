<?php
/**
 * Created by PhpStorm.
 * User: Chlegou
 * Date: 13/03/2018
 * Time: 23:46
 */

include 'GIFEncoder.class.php';
// needed only for <= PHP2.5
include 'php52-fix.php';

//including the imagettftextblur function
include 'imagettftextblur.php';


class CountdownTimerGif
{
    public static function create($imagePath, $time, $timezone, $backgroundColor, $counterColor, $counterGlowColor, $counterGlowSize, $indicatorsColor, $indicatorsGlowColor, $indicatorsGlowSize, $width, $height){
        // http://php.net/manual/en/timezones.php
        // if the given timezone isn't correct, we set it to default UTC timezone
        if(!date_default_timezone_set($timezone)) {
            date_default_timezone_set('UTC');
        }

        // calculate rgb from hexdec color
        list($backgroundColorRed, $backgroundColorGreen, $backgroundColorBlue) = array_map('hexdec', str_split($backgroundColor, 2));

        // calculate rgb from hexdec color
        list($counterColorRed, $counterColorGreen, $counterColorBlue) = array_map('hexdec', str_split($counterColor, 2));
        // calculate rgb from hexdec color
        list($counterGlowColorRed, $counterGlowColorGreen, $counterGlowColorBlue) = array_map('hexdec', str_split($counterGlowColor, 2));

        // calculate rgb from hexdec color
        list($indicatorsColorRed, $indicatorsColorGreen, $indicatorsColorBlue) = array_map('hexdec', str_split($indicatorsColor, 2));
        // calculate rgb from hexdec color
        list($indicatorsGlowColorRed, $indicatorsGlowColorGreen, $indicatorsGlowColorBlue) = array_map('hexdec', str_split($indicatorsGlowColor, 2));

        // default height and width in pixel
        $imageDimensions = getimagesize($imagePath);

        // Your image link
        $image = CountdownTimerGif::copyTransparent($imagePath, $width, $height, $backgroundColorRed, $backgroundColorGreen, $backgroundColorBlue);

        $delay = 100;// milliseconds

        // when changing fonts, we need to adjust the position!!!!
        $font = array(
            'counter' => array(
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
                'color' => imagecolorallocatealpha($image, $counterColorRed, $counterColorGreen, $counterColorBlue, 0), // RGB Colour of the text
                'glowColor' => imagecolorallocatealpha($image, $counterGlowColorRed, $counterGlowColorGreen, $counterGlowColorBlue, 0), // RGB Colour of the text
                'glowSize' => $counterGlowSize,
            ),
            'indicators' => array(
                'size' => 10, // Font size, in pts usually.
                'angle' => 0, // Angle of the text
                // The larger the number the further the distance from the left hand side, 0 to align to the left.
                'x-offset' => array(// +73 offset
                    'days' => 175 - ($imageDimensions[0] - $width) / 2 ,
                    'hours' => 246 - ($imageDimensions[0] - $width) / 2 ,
                    'minutes' => 323 - ($imageDimensions[0] - $width) / 2 ,
                    'seconds' => 400 - ($imageDimensions[0] - $width) / 2 ,
                ),
                'y-offset' => 193 - ($imageDimensions[1] - $height) / 2 , // The vertical alignment, trial and error between 20 and 60.
                'file' => 'fonts/BEBAS.ttf', // Font path
                'color' => imagecolorallocatealpha($image, $indicatorsColorRed, $indicatorsColorGreen, $indicatorsColorBlue, 0), // RGB Colour of the text
                'glowColor' => imagecolorallocatealpha($image, $indicatorsGlowColorRed, $indicatorsGlowColorGreen, $indicatorsGlowColorBlue, 0), // RGB Colour of the text
                'glowSize' => $indicatorsGlowSize,
            ),
        );







        $future_date = new DateTime(date('r',strtotime($time)));
        $time_now = time();
        $now = new DateTime(date('r', $time_now));
        $frames = array();
        $delays = array();

        for($i = 0; $i <= 60; $i++){

            $interval = date_diff($future_date, $now);

            if($future_date < $now){
                // Open the first source image and add the text.
                $image = CountdownTimerGif::copyTransparent($imagePath, $width, $height, $backgroundColorRed, $backgroundColorGreen, $backgroundColorBlue);

                $image = CountdownTimerGif::applyIndicatorsToImage($image, $font);

                // add the glow first
                if ($font['counter']['glowSize'] > 0) {
                    // days
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['days'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], '00', $font['counter']['glowSize'] );
                    // hours
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['hours'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], '00', $font['counter']['glowSize'] );
                    // minutes
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['minutes'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], '00', $font['counter']['glowSize'] );
                    // seconds
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['seconds'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], '00', $font['counter']['glowSize'] );

                }

                // days
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['days'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], '00' );
                // hours
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['hours'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], '00' );
                // minutes
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['minutes'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], '00' );
                // seconds
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['seconds'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], '00' );

                ob_start();
                imagegif($image);
                $frames[]=ob_get_contents();
                $delays[]=$delay;
                $loops = 1;
                ob_end_clean();
                break;
            } else {
                // Open the first source image and add the text.
                $image = CountdownTimerGif::copyTransparent($imagePath, $width, $height, $backgroundColorRed, $backgroundColorGreen, $backgroundColorBlue);

                $image = CountdownTimerGif::applyIndicatorsToImage($image, $font);

                $days = $interval->format('%a');
                //add zero padding for days
                if(strlen($days) == 1){
                    $days = '0'.$days;
                }

                // add the glow first
                if ($font['counter']['glowSize'] > 0) {
                    // days
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['days'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], $days, $font['counter']['glowSize'] );
                    // hours
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['hours'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], $interval->format('%H'), $font['counter']['glowSize'] );
                    // minutes
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['minutes'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], $interval->format('%I'), $font['counter']['glowSize'] );
                    // seconds
                    imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['seconds'] , $font['counter']['y-offset'] , $font['counter']['glowColor'] , $font['counter']['file'], $interval->format('%S'), $font['counter']['glowSize'] );

                }

                // days
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['days'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], $days );
                // hours
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['hours'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], $interval->format('%H') );
                // minutes
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['minutes'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], $interval->format('%I') );
                // seconds
                imagettftextblur ($image , $font['counter']['size'] , $font['counter']['angle'] , $font['counter']['x-offset']['seconds'] , $font['counter']['y-offset'] , $font['counter']['color'] , $font['counter']['file'], $interval->format('%S') );

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
        return $gif;

    }

    // this function will create a centered copy from the source file image.
    private static function copyTransparent($src, $newWidth, $newHeight, $red, $green, $blue)
    {
        $dimensions = getimagesize($src);
        $x = $dimensions[0];
        $y = $dimensions[1];

        $im = imagecreatetruecolor( $newWidth, $newHeight );
        $src_ = imagecreatefrompng($src);
        // Prepare alpha channel for transparent background
        // alpha must be zero!!!!
        $alpha_channel = imagecolorallocatealpha($im, $red, $green, $blue, 0);
        /*
         * UPDATE_2018-03-16:
         * In a way to get real black background, we must bypass this function
         * it was making it transparent in #000000 example.
         * Instead, like this example, #775151, it must be used to clear the background.
         */
        if(!($red === 0 && $green === 0 && $blue ===0)){
            imagecolortransparent($im, $alpha_channel);
        }
        // Fill image
        imagefill($im, 0, 0, $alpha_channel);
        // Copy from other
        imagecopy($im,$src_, ($x - $newWidth < 0) ? ($newWidth- $x) / 2 : 0, ($y - $newHeight < 0) ? ($newHeight- $y) / 2 : 0, ($x - $newWidth >= 0) ? ($x - $newWidth) / 2 : 0, ($y - $newHeight >= 0) ? ($y - $newHeight) / 2 : 0, $x, $y);
        imagesavealpha($im,true);

        return $im;
    }


    private static function applyIndicatorsToImage($image, $font){
        /** if we need textBlur or add shadow, the we need to integrate this library
         * link: https://github.com/andrewgjohnson/imagettftextblur
         */

        // add indicators glow to image
        if ($font['indicators']['glowSize'] > 0) {
            // days
            imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['days'] , $font['indicators']['y-offset'] , $font['indicators']['glowColor'] , $font['indicators']['file'], 'DAYS', $font['indicators']['glowSize'] );
            // hours
            imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['hours'] , $font['indicators']['y-offset'] , $font['indicators']['glowColor'] , $font['indicators']['file'], 'HOURS', $font['indicators']['glowSize'] );
            // minutes
            imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['minutes'] , $font['indicators']['y-offset'] , $font['indicators']['glowColor'] , $font['indicators']['file'], 'MINS', $font['indicators']['glowSize'] );
            // seconds
            imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['seconds'] , $font['indicators']['y-offset'] , $font['indicators']['glowColor'] , $font['indicators']['file'], 'SEC', $font['indicators']['glowSize'] );

        }

        // add indicators to image
        // days
        imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['days'] , $font['indicators']['y-offset'] , $font['indicators']['color'] , $font['indicators']['file'], 'DAYS' );
        // hours
        imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['hours'] , $font['indicators']['y-offset'] , $font['indicators']['color'] , $font['indicators']['file'], 'HOURS' );
        // minutes
        imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['minutes'] , $font['indicators']['y-offset'] , $font['indicators']['color'] , $font['indicators']['file'], 'MINS' );
        // seconds
        imagettftextblur ($image , $font['indicators']['size'] , $font['indicators']['angle'] , $font['indicators']['x-offset']['seconds'] , $font['indicators']['y-offset'] , $font['indicators']['color'] , $font['indicators']['file'], 'SEC' );

        return $image;
    }



}