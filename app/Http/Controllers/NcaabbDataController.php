<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client as CrawlerClient;

use App\Library;
use App\Library\ScoresOddsScraper;
use App\Library\Format\DailyLinesEmail;
use App\Library\Format\RoundballEmail;

use App\Library\CoversComScraper;

class NcaabbDataController extends Controller
{   
    // Output an "email" formatted spread
    public function emailFormatData() {
        
        // Collect the data
        $dataCollector  = new ScoresOddsScraper('bkc');
        $data           = $dataCollector->scrape_all();
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new DailyLinesEmail($data,'bkc');
        $email  = $format->create_email_text();
        
        return $email;
    }
    
    // Output an "email" formatted spread for tomorrow
    public function emailFormatDataTomorrow() {
        
        // Collect the data
        $dataCollector  = new ScoresOddsScraper('bkc');
        $data           = $dataCollector->scrape_all('overnight');
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new DailyLinesEmail($data,'bkc');
        $email  = $format->create_email_text();
        
        return $email;
    }
    
    // Output an "email" formatted spread for yesterday
    public function emailFormatDataYesterday() {
        
        // Collect the data
        $dataCollector  = new ScoresOddsScraper('bkc');
        $data           = $dataCollector->scrape_all('yesterday');
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new DailyLinesEmail($data,'bkc');
        $email  = $format->create_email_text();
        
        return $email;
    }
    
    // Return the data in plain text
    public function showEmailFormatDataPlainText() {
        $data = $this->emailFormatData();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
    public function showEmailFormatDataTomorrowPlainText() {
        $data = $this->emailFormatDataTomorrow();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
    public function showEmailFormatDataYesterdayPlainText() {
        $data = $this->emailFormatDataYesterday();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
    // Output an "email"
    public function emailFormatRoundball() {
        
        // Collect the data
        $dataCollector  = new CoversComScraper('bkc');
        $data           = $dataCollector->scrape_print_sheet();
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new RoundballEmail($data,'bkc');
        $email  = $format->create_email_text();
        return $email;
    }
    
    public function showRoundBallPlainText() {
        $data = $this->emailFormatRoundball();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
}
