<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Mail\PlainTextGeneric;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// All the sports
use App\Http\Controllers\NbaDataController;
use App\Http\Controllers\NcaabbDataController;
use App\Http\Controllers\NcaafbController;
use App\Http\Controllers\NflDataController;
use App\Http\Controllers\NhlDataController;

use App\Http\Controllers\FootballScheduleController;

class MailTest extends Controller
{
    
    public $emails;
    
    public function __construct() {
        $this->emails = array('jake@nerdyhouse.com','mjrmjr19@gmail.com');
    }
    
    /*
     *  Sends
     */
    // NBA
    public function sendNba() {
        try {
            $cont   = new NbaDataController();
            $data   = $cont->emailFormatData();
            $this->send_emails($data,'NBA: TODAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNbaTomorrow() {
        try {
            $cont   = new NbaDataController();
            $data   = $cont->emailFormatDataTomorrow();
            $this->send_emails($data,'NBA: TOMORROW');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNbaYesterday() {
        try {
            $cont   = new NbaDataController();
            $data   = $cont->emailFormatDataYesterday();
            $this->send_emails($data,'NBA: YESTERDAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    // NCAA BB
    public function sendNcaabb() {
        try {
            $cont   = new NcaabbDataController();
            $data   = $cont->emailFormatData();
            $this->send_emails($data,'NCAA BB: TODAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNcaabbTomorrow() {
        try {
            $cont   = new NcaabbDataController();
            $data   = $cont->emailFormatDataTomorrow();
            $this->send_emails($data,'NCAA BB: TOMORROW');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNcaabbYesterday() {
        try {
            $cont   = new NcaabbDataController();
            $data   = $cont->emailFormatDataYesterday();
            $this->send_emails($data,'NCAA BB: YESTERDAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    // NCAA FB
    public function sendNcaafb() {
        try {
            $cont   = new NcaafbController();
            $data   = $cont->emailFormatData();
            $this->send_emails($data,'NCAA FB: TODAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNcaafbTomorrow() {
        try {
            $cont   = new NcaafbController();
            $data   = $cont->emailFormatDataTomorrow();
            $this->send_emails($data,'NCAA FB: TOMORROW');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNcaafbYesterday() {
        try {
            $cont   = new NcaafbController();
            $data   = $cont->emailFormatDataYesterday();
            $this->send_emails($data,'NCAA FB: YESTERDAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    // NFL
    public function sendNfl() {
        try {
            $cont   = new NflDataController();
            $data   = $cont->emailFormatData();
            $this->send_emails($data,'NFL: TODAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNflTomorrow() {
        try {
            $cont   = new NflDataController();
            $data   = $cont->emailFormatDataTomorrow();
            $this->send_emails($data,'NFL: TOMORROW');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNflYesterday() {
        try {
            $cont   = new NflDataController();
            $data   = $cont->emailFormatDataYesterday();
            $this->send_emails($data,'NFL: YESTERDAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    // NHL
    public function sendNhl() {
        try {
            $cont   = new NhlDataController();
            $data   = $cont->emailFormatData();
            $this->send_emails($data,'NHL: TODAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNhlTomorrow() {
        try {
            $cont   = new NhlDataController();
            $data   = $cont->emailFormatDataTomorrow();
            $this->send_emails($data,'NHL: TOMORROW');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNhlYesterday() {
        try {
            $cont   = new NhlDataController();
            $data   = $cont->emailFormatDataYesterday();
            $this->send_emails($data,'NHL: YESTERDAY');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    // WEEKLY & BOWLS
    public function sendNflThisWeek() {
        try {
            $cont   = new FootballScheduleController();
            $data   = $cont->getNflSchedule();
            $this->send_emails($data,'NFL: THIS WEEK');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNflNextWeek() {
        try {
            $cont   = new FootballScheduleController();
            $data   = $cont->getNextNflSchedule();
            $this->send_emails($data,'NFL: NEXT WEEK');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNcaafbThisWeek() {
        try {
            $cont   = new FootballScheduleController();
            $data   = $cont->getNcaaFbSchedule();
            $this->send_emails($data,'NCAA FB: THIS WEEK');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNcaafbNextWeek() {
        try {
            $cont   = new FootballScheduleController();
            $data   = $cont->getNextNcaaFbSchedule();
            $this->send_emails($data,'NCAA FB: NEXT WEEK');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function sendNcaafbBowls() {
        try {
            $cont   = new FootballScheduleController();
            $data   = $cont->getNcaaFbBowlSchedule();
            $this->send_emails($data,'NCAA FB BOWLS');
            return redirect('send');
        } catch (\Exception $e) {
            return $this->handle_exception($e);
        }
    }
    
    public function send_emails($data,$subj) {
        $emails = explode('##### NEW EMAIL #####', $data);
        for($i = 0; $i < count($emails); $i++) {
            Mail::to($this->emails)->send(new PlainTextGeneric($emails[$i],$subj.' #'.($i+1)));
        }
    }
    
    public function handle_exception($e) {
        Log::error('Error sending the test email : '.$e);
        return response('Error sending test email',400)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
}
