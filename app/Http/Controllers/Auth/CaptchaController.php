<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function image(Request $request)
    {
        // generate a 4-letter random string (uppercase)
        $letters = '';
        for ($i = 0; $i < 4; $i++) {
            $letters .= chr(rand(65, 90));
        }

        // store answer and expiry in session
        Session::put('password_reset_captcha_answer', $letters);
        Session::put('password_reset_captcha_expires_at', now()->addMinutes(15)->timestamp);

        // canvas size
        $width = 240;
        $height = 90;

        // unique filter id to avoid re-use
        $filterId = 'f' . dechex(rand(0, 0xFFFF));

        // start svg
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='$width' height='$height' viewBox='0 0 $width $height'>";

        // defs: turbulence + displacement to warp letters
        $seed = rand(0, 10000);
        $baseFreq = rand(5, 15) / 1000; // small frequency
        $scale = rand(6, 20); // displacement strength
        $svg .= "<defs>";
        $svg .= "<filter id='$filterId'><feTurbulence type='fractalNoise' baseFrequency='$baseFreq' numOctaves='2' seed='$seed' stitchTiles='stitch'/>";
        $svg .= "<feDisplacementMap in='SourceGraphic' scale='$scale' xChannelSelector='R' yChannelSelector='G'/>";
        $svg .= "</filter>";
        // small text shadow filter
        $svg .= "<filter id='s' x='-20%' y='-20%' width='140%' height='140%'><feGaussianBlur stdDeviation='1'/></filter>";
        $svg .= "</defs>";

        // background with subtle gradient
        $svg .= "<defs><linearGradient id='g' x1='0' x2='1'><stop offset='0' stop-color='#fbfbfb'/><stop offset='1' stop-color='#eef2f5'/></linearGradient></defs>";
        $svg .= "<rect width='100%' height='100%' fill='url(#g)'/>";

        // add noisy lines and curves
        for ($i = 0; $i < 14; $i++) {
            $x1 = rand(-20, $width);
            $y1 = rand(-10, $height + 10);
            $x2 = rand(-20, $width);
            $y2 = rand(-10, $height + 10);
            $cx = rand(0, $width);
            $cy = rand(0, $height);
            $color = sprintf('#%06X', rand(0x555555, 0xCCCCCC));
            $opacity = rand(20, 60) / 100;
            $strokeW = rand(1, 2);
            $svg .= "<path d='M$x1 $y1 Q$cx $cy $x2 $y2' stroke='$color' stroke-width='$strokeW' fill='none' opacity='$opacity'/>";
        }

        // sprinkle dots
        for ($i = 0; $i < 80; $i++) {
            $cx = rand(0, $width);
            $cy = rand(0, $height);
            $r = rand(0, 2);
            $color = sprintf('#%06X', rand(0x666666, 0xBBBBBB));
            $svg .= "<circle cx='$cx' cy='$cy' r='$r' fill='$color' opacity='0.6'/>";
        }

        // fonts to randomize per-letter
        $fonts = ['Arial', 'Verdana', 'Georgia', 'Courier New', 'Tahoma', 'Times New Roman'];

        // draw each letter with random transforms and apply displacement filter to whole letter group
        $groupX = 18;
        $fontSize = rand(36, 48);
        $spacing = intval($width / (strlen($letters) + 1));
        $svg .= "<g filter='url(#$filterId)'>";
        for ($i = 0; $i < strlen($letters); $i++) {
            $char = $letters[$i];
            $x = $groupX + $i * $spacing + rand(-6, 6);
            $y = intval($height / 2) + rand(6, 14);
            $rotate = rand(-45, 45);
            $skewX = rand(-20, 20);
            $skewY = rand(-15, 15);
            $tx = rand(-4, 4);
            $ty = rand(-6, 6);
            $font = $fonts[array_rand($fonts)];
            $weight = (rand(0, 10) > 6) ? 'bold' : 'normal';
            $color = sprintf('#%06X', rand(0x101010, 0x333333));

            $transform = "translate($tx $ty) rotate($rotate $x $y) skewX($skewX) skewY($skewY)";
            $svg .= "<text x='$x' y='$y' font-family='$font' font-size='$fontSize' font-weight='$weight' fill='$color' transform='$transform' style='letter-spacing:2px;filter:url(#s)'>$char</text>";
        }
        $svg .= "</g>";

        // overlay subtle strokes to further obscure
        for ($i = 0; $i < 6; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = rand(0, $width);
            $y2 = rand(0, $height);
            $color = sprintf('#%06X', rand(0x444444, 0x888888));
            $svg .= "<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' stroke='$color' stroke-width='1' opacity='0.5'/>";
        }

        $svg .= "</svg>";

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }
}
