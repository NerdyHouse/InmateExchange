<?php

namespace App\Library;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\FileCookieJar;
use Goutte\Client as CrawlerClient;

use Illuminate\Support\Facades\Log;

use App\Library\Utilities;

/**
 * Description of CorrLinks
 *
 * @author jacobreadinger
 */
class CorrLinks {
    
    public $credentials;
    public $credindex;
    public $client;
    public $connection;
    public $status;
    
    public function __construct() {
        $this->credentials = array(
            array('un' => 'simontab8@gmail.com','pw' => '23451##Aa'),
            array('un' => 'foo@gmail.com','pw' => 'dsaasd')
        );
        $this->credindex = 0;
    }
    
    // get status
    public function getConnectionStatus() {
        return $this->status;
    }
    
    // Get human readable message
    public function getMessage() {
        $status  = $this->getConnectionStatus();
        $message = "";
        switch ($status) {
            case 0:
                $message = "A manual Corrlinks connection is needed.";
                break;
            case 1:
                $message = "Please complete the captcha to complete the manual connection.";
                break;
            case 2:
                $message = "Connection is open!";
            default;
        }
        
        return $message;
    }
    
    // get the current page using unique elements
    public function getCurrentScreen() {
        // check for the username input
        $login1     = $this->connection->filter('#ctl00_mainContentPlaceHolder_loginUserNameTextBox')->count();
        
        // Check for the captcha code input
        $captcha    = $this->connection->filter('#ctl00_mainContentPlaceHolder_CaptchaCodeTextBox')->count();
        
        // Check for email image on home screen
        $mail       = $this->connection->filter('#ctl00_mainContentPlaceHolder_mailboxImageButton')->count();
        
        if($login1 > 0) { return 'login'; }
        if($captcha > 0 ) { return 'captcha'; }
        if($mail > 0 ) { return 'home'; }
    }
    
    // establish initial connection
    public function initial_connect($url) {
        try {
            $client = new CrawlerClient();
            
            $cookieFile     = storage_path('app/jar.txt');
            $cookieJar      = new FileCookieJar($cookieFile, TRUE);
            
            
            $guzzleClient   = new GuzzleClient(array(
                'timeout'   => 30,
                'verify'    => false,
                'cookies'   => $cookieJar,
                'debug'     => true
            ));
            
            $client->setClient($guzzleClient);
            $client->setGuzzleCookieJar($cookieJar);
            
            $this->client = $client;
        
            $crawler = $client->request('GET',$url);
            
        } catch (\Exception $e) {
            Log::error('Failed to connect to CorrLinks: '.$e);
            return false;
        }
        return $crawler;
    }
    
    // Attempt to connect and see what happens...
    public function init() {
        
        // Attempt to establish an initial connection
        //$connection = $this->initial_connect('https://www.corrlinks.com/Login.aspx');
        $connection = $this->initial_connect('http://localhost:8888/NewFolder/');
        if(!$connection) { return false; };
        
        // Set the connection globally to reuse easily
        $this->connection = $connection;
        
        // check the page and delagate accordingly...
        if($this->getCurrentScreen() === 'login') {
            return $this->needConnection();
        } else if($this->getCurrentScreen() === 'captcha') {
            return $this->needCaptcha();
        }
        else {
            return $this->run();
        }
    }
    
    // offer up a manual connection process
    public function needConnection() {
        $this->status = 0;
        return ['connection' => $this->getConnectionStatus(),'message' => $this->getMessage()];
    }
    
    // Handle captcha data
    public function getCaptchaImgSrc() {
        
        // we check if the image exists and either grab it or return empty string
        $captchaImg     = $this->connection->filter('img.LBD_CaptchaImage')->count();
        if($captchaImg > 0) {
            $captchaImg     = $this->connection->filter('img.LBD_CaptchaImage')->image();
            $captchaSrc     = $captchaImg->getUri();
            return $captchaSrc;
        }
        return "";
    }
    public function needCaptcha() {
        $this->status   = 1;
        $captchaSrc = $this->getCaptchaImgSrc();
        return ['connection' => $this->getConnectionStatus(),'message' => $this->getMessage(),'img' => $captchaSrc];
    }
    
    // attempt to login
    public function login() {
        $form = $this->connection->filter('#aspnetForm')->form();
        $form['ctl00$mainContentPlaceHolder$loginUserNameTextBox'] = $this->credentials[$this->credindex]['un'];
        $form['ctl00$mainContentPlaceHolder$loginPasswordTextBox'] = $this->credentials[$this->credindex]['pw'];
        $this->connection = $this->client->submit($form);
        
        if($this->getCurrentScreen() === 'captcha') {
            return $this->needCaptcha();
        }
        return ['connection' => $this->getConnectionStatus(),'message' => $this->getMessage(),'img' => $captchaSrc];
    }
    
    // attempt to complete captcha
    public function captcha($captcha) {
        $form = $this->connection->filter('#aspnetForm')->form();
        $form['ctl00$mainContentPlaceHolder$CaptchaCodeTextBox'] = $captcha;
        $this->connection = $this->client->submit($form);
        
        $element = $this->connection->filter('#ctl00_mainContentPlaceHolder_mailboxImageButton')->count(); 
        if($element > 0) {
            $this->status = 2;
            $this->run();
        }
        return ['connection' => $this->getConnectionStatus(),'message' => $this->getMessage()];
    }
    
    // handles running actions once login is completed
    public function run() {
        $status = $this->connectionStatus();
    }

    // If we visit while the connection is good we show some status of the connection
    public function connectionStatus() {
        //return $this->connection;
        return "connection is good!";
    }
    
}
