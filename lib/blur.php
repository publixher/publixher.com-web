<?php

/**
 * Strong Blur
 *
 * @param resource $gdImageResource
 * @param int $blurFactor optional
 *  This is the strength of the blur
 *  0 = no blur, 3 = default, anything over 5 is extremely blurred
 * @return GD image resource
 * @author Martijn Frazer, idea based on http://stackoverflow.com/a/20264482
 */
function blur($gdImageResource, $blurFactor = 3)
{
    //TODO:path 도메인에 맞춰 바꿔야함
    // blurFactor has to be an integer
    $blurFactor = round($blurFactor);
    $filepath=__DIR__.'/..'. $gdImageResource;
    $filename=basename($filepath);
    $outpath=__DIR__.'/../img/blur/'.$filename;
    //get image resource
    $ext = strtoupper(pathinfo($gdImageResource, PATHINFO_EXTENSION));
    if($ext == "JPG" OR $ext == "JPEG"){
        $gdImageResource= imagecreatefromjpeg($filepath);
    }else if($ext == "PNG"){
        $gdImageResource= imagecreatefrompng($filepath);
    }else if($ext == "GIF"){
        $gdImageResource= imagecreatefromgif($filepath);
    }
    //get image size
    $originalWidth = imagesx($gdImageResource);
    $originalHeight = imagesy($gdImageResource);

    $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
    $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

    // for the first run, the previous image is the original input
    $prevImage = $gdImageResource;
    $prevWidth = $originalWidth;
    $prevHeight = $originalHeight;

    // scale way down and gradually scale back up, blurring all the way
    for($i = 0; $i < $blurFactor; $i += 1)
    {
        // determine dimensions of next image
        $nextWidth = $smallestWidth * pow(2, $i);
        $nextHeight = $smallestHeight * pow(2, $i);

        // resize previous image to next size
        $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
        imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
            $nextWidth, $nextHeight, $prevWidth, $prevHeight);

        // apply blur filter
        imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

        // now the new image becomes the previous image for the next step
        $prevImage = $nextImage;
        $prevWidth = $nextWidth;
        $prevHeight = $nextHeight;
    }

    // scale back to original size and blur one more time
    imagecopyresized($gdImageResource, $nextImage,
        0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
    imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);

    //save image
    $bluredImg=ImageCreateTrueColor(100, 100);
    ImageCopyResampled($bluredImg, $gdImageResource, 0, 0, 0, 0, 100, 100, $originalWidth, $originalHeight);
    if($ext == "JPG" OR $ext == "JPEG"){
        imagejpeg($bluredImg,$outpath);
    }else if($ext == "PNG"){
        imagepng($bluredImg,$outpath);
    }else if($ext == "GIF"){
        imagegif($bluredImg,$outpath);
    }
    // clean up
    imagedestroy($prevImage);
    imagedestroy($gdImageResource);
    imagedestroy($bluredImg);
    // return result
    return '/img/blur/'.$filename;
}
?>