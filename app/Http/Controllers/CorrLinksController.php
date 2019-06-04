<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\CorrLinks;


class CorrLinksController extends Controller
{
    
    // Initiates Corrlinks functionality
    public function init() {
        $CL             = new CorrLinks();
        $connection     = $CL->init();
        $return = $CL;
        return $return;
    }
    
    // Handles the manual connection request
    public function connect(Request $request) {
        $connection = $this->init();
        $captchaimg = $connection->getCaptchaImgSrc();
        
        // Handle initial login
        if($request->has('clconnect')) {
            if($request->clconnect === 'connect') {
                $login = $connection->login();
                $captchaimg = $login['img'];
                return view('corrlinks/connection',['status' => $connection->getConnectionStatus(),'message' => $connection->getMessage(),'img' => $captchaimg]);
            }
        }
        
        // Handle the captcha
        if($request->has('clcaptcha')) {
            $connection->captcha($request->clcaptcha);
            if($connection->getConnectionStatus() === 2) {
                return view('corrlinks/status',['status' => $connection->getConnectionStatus(),'message' => $connection->getMessage(),'img' => $captchaimg,'info' => $connection->connectionStatus()]);
            }
        }
        
        return view('corrlinks/connection',['status' => $connection->getConnectionStatus(),'message' => $connection->getMessage(),'img' => $captchaimg]);
    }

    // Handle the captcha form
    public function captcha(Request $request) {
        
    }
}
