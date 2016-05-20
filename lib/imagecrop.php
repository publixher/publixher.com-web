<?php
ini_set('memory_limit', -1);    //이미지 로테이션시켜야할때 메모리 제한 없앰
// Imaging
class imaging
{

    // Variables
    private $img_input;
    private $img_output;
    private $img_src;
    private $format;
    private $quality = 80;
    private $x_input;
    private $y_input;
    private $x_output;
    private $y_output;
    private $resize;
    private $order=0;

    //Set order
    public function set_order($order){
        //0은 긴축기준 1은 짧은축 기준
        $this->order=$order;
    }

    // Set image
    public function set_img($img)
    {

        // Find format
        $ext = strtoupper(pathinfo($img, PATHINFO_EXTENSION));
        $exif=exif_read_data($img);

        // JPEG image
        if(is_file($img) && ($ext == "JPG" OR $ext == "JPEG"))
        {

            $this->format = $ext;
            $this->img_input = ImageCreateFromJPEG($img);
            $this->img_src = $img;

        }

        // PNG image
        elseif(is_file($img) && $ext == "PNG")
        {

            $this->format = $ext;
            $this->img_input = ImageCreateFromPNG($img);
            $this->img_src = $img;

        }

        // GIF image
        elseif(is_file($img) && $ext == "GIF")
        {

            $this->format = $ext;
            $this->img_input = ImageCreateFromGIF($img);
            $this->img_src = $img;

        }
        if(!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 8:
                    $this->img_input = imagerotate($this->img_input, 90, 0);
                    break;
                case 3:
                    $this->img_input = imagerotate($this->img_input, 180, 0);
                    break;
                case 6:
                    $this->img_input = imagerotate($this->img_input, -90, 0);
                    break;
            }
        }
        // Get dimensions
        $this->x_input = imagesx($this->img_input);
        $this->y_input = imagesy($this->img_input);

    }

    // Set maximum image size (pixels)
    public function set_size($sizeW = 100,$sizeH=100)
    {

        // Resize
        if($this->x_input > $sizeW OR $this->y_input > $sizeH)
        {
            // Wide
            if(($this->order==0 and $this->x_input >= $this->y_input) or ($this->order==1 and $this->x_input<=$this->y_input))
            {

                $this->x_output = $sizeW;
                $this->y_output = ($this->x_output / $this->x_input) * $this->y_input;

            }

            // Tall
            else
            {

                $this->y_output = $sizeH;
                $this->x_output = ($this->y_output / $this->y_input) * $this->x_input;

            }

            // Ready
            $this->resize = TRUE;

        }

        // Don't resize
        else { $this->resize = FALSE; }

    }

    // Set image quality (JPEG only)
    public function set_quality($quality)
    {

        if(is_int($quality))
        {

            $this->quality = $quality;

        }

    }

    // Save image
    public function save_img($path)
    {

        // Resize
        if($this->resize)
        {

            $this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output);
            if($this->format=="PNG"){
                $background = imagecolorallocate($this->img_output, 0, 0, 0);
                imagecolortransparent($this->img_output, $background);
                imagealphablending($this->img_output, false);
                imagesavealpha($this->img_output, true);
            }
            ImageCopyResampled($this->img_output, $this->img_input, 0, 0, 0, 0, $this->x_output, $this->y_output, $this->x_input, $this->y_input);

        }

        // Save JPEG
        if($this->format == "JPG" OR $this->format == "JPEG")
        {

            if($this->resize) { imageJPEG($this->img_output, $path, $this->quality); }
            else { copy($this->img_src, $path); }

        }

        // Save PNG
        elseif($this->format == "PNG")
        {

            if($this->resize) { imagePNG($this->img_output, $path); }
            else { copy($this->img_src, $path); }

        }

        // Save GIF
        elseif($this->format == "GIF")
        {

            if($this->resize) { imageGIF($this->img_output, $path); }
            else { copy($this->img_src, $path); }

        }

    }

    // Get width
    public function get_width()
    {

        return $this->x_input;

    }

    // Get height
    public function get_height()
    {

        return $this->y_input;

    }

    // Get width
    public function get_out_width()
    {

        return $this->x_output?$this->x_output:$this->x_input;

    }

    // Get height
    public function get_out_height()
    {

        return $this->y_output?$this->y_output:$this->y_input;

    }

    // Clear image cache
    public function clear_cache()
    {

        @ImageDestroy($this->img_input);
        @ImageDestroy($this->img_output);

    }

}
?>