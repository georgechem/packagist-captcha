<?php

namespace Georgechem\Captcha\Captcha;
/**
 * Singleton
 */
class Captcha
{
    const TIMEOUT = 5;
    const FONT_NAME = 'font.ttf';
    private int $width = 180;
    private int $height = 60;
    private string $fontPath;
    private static ?Captcha $instance = null;
    private ?\GdImage $img = null;
    private ?string $text = null;
    private int $length = 5;

    private function __construct()
    {
        $this->init();
    }
    /**
     * @return Captcha|null
     */
    public static function getInstance(): ?Captcha
    {
        if(empty(self::$instance)){
            self::$instance = new Captcha();
        }
        return self::$instance;
    }

    /**
     * Init Temporary Session Storage for Captcha
     */
    private function initSessionStorage()
    {
        session_start([
            'name' => 'captchaSession',
            'cookie_lifetime' => self::TIMEOUT * 60,
            'cookie_httponly' => '1',
            'cookie_samesite' => '1',
            'use_cookies' => '1',
        ]);
    }
    /**
     * Initialize and set up font path
     */
    private function init()
    {
        $this->initSessionStorage();
        $this->fontPath = dirname(__DIR__, 1) . '/fonts/';
        putenv('GDFONTPATH=' . $this->fontPath);
    }
    /**
     * Create Captcha using GD library
     */
    private function createCaptcha()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        imageantialias($this->img, true);
        $bgColor = imagecolorallocate($this->img, 255, 255, 255);
        $fontColor = imagecolorallocate($this->img, 0, 0, 0);
        imagefill($this->img, 0, 0, $bgColor);
        $j = 0;

        for($i=130; $i>80; $i-=0.25){
            $col = (int) $i;
            $color = imagecolorallocate($this->img, $col, $col, $col);
            $posX = rand(0, $this->width);
            imagerectangle($this->img, $j, 0, $this->width - 1, $this->height - 1, $color);
            $j++;
        }

        for($i=0; $i<$this->length; $i++){
            $number = rand(65, 122);
            if($number >= 91 && $number <=96){
                $i--;
                continue;
            }
            $this->text .= chr($number);
        }

        $_SESSION['captcha'] = $this->text;
        imagettftext($this->img, 35, 0, 10, 45, $fontColor, self::FONT_NAME, $this->text);
    }

    private function distortCaptcha()
    {
        imagefilter($this->img, IMG_FILTER_PIXELATE, 2);
        imagefilter($this->img, IMG_FILTER_COLORIZE, 100, 100, 100, 100);
        imagefilter($this->img, IMG_FILTER_SMOOTH, 9);
        imagefilter($this->img, IMG_FILTER_GAUSSIAN_BLUR);

    }

    /**
     * Create and echo captcha
     */
    public function create()
    {
        $this->createCaptcha();
        $this->distortCaptcha();
        header('Content-Type: image/jpeg');
        echo imagejpeg($this->img, null, 25);
    }

    public function verify(string $text):bool
    {
        $captcha = $_SESSION['captcha'] ?? null;
        if($captcha === $text) return true;
        return false;
    }

}