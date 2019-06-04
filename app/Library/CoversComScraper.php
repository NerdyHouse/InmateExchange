<?php
/**
 * Description of ScoresOddsScraper
 *
 * @author jacobreadinger
 */

namespace App\Library;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client as CrawlerClient;

use Illuminate\Support\Facades\Log;

use App\Library\Utilities;

class CoversComScraper {
    
    public $_sport;
    public $_urls;
    
    public function __construct($sport = 'all') {
        $this->_sport = $sport;
        
        $this->_urls = array(
            'nfl'       => 'https://www.covers.com/Sports/NFL/PrintSheetHtml?isPrevious=False',
            'nba'       => 'https://www.covers.com/Sports/NBA/PrintSheetHtml?isPrevious=False',
            'mlb'       => 'https://www.covers.com/Sports/MLB/PrintSheetHtml?isPrevious=False',
            'nhl'       => 'https://www.covers.com/Sports/NHL/PrintSheetHtml?isPrevious=False',
            'fbc'       => 'https://www.covers.com/Sports/NCAAF/PrintSheetHtml?isPrevious=False',
            'bkc'       => 'https://www.covers.com/Sports/NCAAB/PrintSheetHtml?isPrevious=False',
            'nascar'    => 'https://www.covers.com/Sports/NASCAR/PrintSheetHtml?isPrevious=False',
            'wnba'      => 'https://www.covers.com/Sports/WNBA/PrintSheetHtml?isPrevious=False',
            'afl'       => 'https://www.covers.com/Sports/AFL/PrintSheetHtml?isPrevious=False',
            'cfl'       => 'https://www.covers.com/Sports/CFL/PrintSheetHtml?isPrevious=False'
        );
    }
    
    // Scrape homepage stats for sport
    public function crawler_connect($url) {
        
        // Try to connect
        try {
            
            $client = new CrawlerClient();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 30,
                'verify'    => false
            ));
            $client->setClient($guzzleClient);
        
            $crawler = $client->request('GET',$url);
            
        } catch(\Exception $e) {
            // Log and return response
            Log::error('Crawler failed to connect: '.$url.' : '.$e);
            return false;
        }
        
        // Return the crawler if we've connected
        return $crawler;
    }
    
    /***************
     * Scrape homepage for games and stats
     */
    public function scrape_print_sheet() {
        
        // Attempt connection
        $url = $this->_urls[trim(strtolower($this->_sport))];
        if(!$url) { return false; }
        $crawler = $this->crawler_connect($url);
        if(!$crawler) { return false; }
        
        $dataArr    = array('cols' => 0);
        
        $today = date('m/d/y');
        
        // We need the second table
        // the top table is covers branding DONT NEED IT
        // Third table is legend DONT NEED IT
        // So we get the second table and we are just going to read
        // every row and put the text in our own array
        $row = 0;
        $crawler->filter('table:nth-child(2) > tr')->each(function($node) use(&$row,&$dataArr) {
            $totalCols = 0;
            $dataArr['rows'][$row] = array();
            $node->filter('td')->each(function($node) use(&$row,&$dataArr,&$totalCols) {
                $totalCols++;
                $dataArr['rows'][$row][] = array(
                    'val'  => trim(str_replace(chr(194),"",$node->text())),
                    'cols' => null !== $node->attr('colspan') ? intval($node->attr('colspan')) : 1
                );
                if(intval($totalCols) > intval($dataArr['cols'])) {
                    $dataArr['cols'] = intval($totalCols);
                }
            });
            $row++;
        });
        // Return the response
        if(!empty($dataArr)) {
            return $dataArr;
        } else {
            false;
        }
    }
}
