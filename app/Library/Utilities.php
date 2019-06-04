<?php

namespace App\Library;

/**
 * Description of Utilities
 *
 * @author jacobreadinger
 */
class Utilities {
    
    // Gets the proper link to click for halftime info
    public static function halftime_link($sport) {
        $links = array(
            'bkc' => 'NCAA 1st Half Lines',
            'nba' => 'NBA 1st Half Lines',
            'fbc' => 'College First Half Lines',
            'nfl' => 'NFL First Half Lines',
            'nhl' => false
        );
        
        if(array_key_exists($sport, $links)) {
            return $links[$sport];
        } else {
            return false;
        }
    }
    
    // Gets the proper lsection for direct ht link
    public static function halftime_section($sport) {
        $links = array(
            'bkc' => 332,
            'nba' => 98,
            'fbc' => 511,
            'nfl' => 11,
            'nhl' => false
        );
        
        if(array_key_exists($sport, $links)) {
            return $links[$sport];
        } else {
            return false;
        }
    }
    
    // Checks and shortens team names
    public static function shorten_team_name($team) {
        
        $shortNames = array(
            
            // NFL
            'new england patriots'      => 'new england pats',
            'san francisco 49ers'       => 'san fran 49ers',
            'tampa bay buccaneers'      => 'tampa bay bucs',
            'jacksonville jaguars'      => 'jacksonville jags',
            'los angeles chargers'      => 'la chargers',
            
            // FBC
            'north carolina state'      => 'nc state',
            'florida international'     => 'florida intl',
            
            // NHL
            'columbus blue jackets'     => 'columbus bjackets',
            
            // NBA
            'minnesota timberwolves'    => 'minnesota twvoles',
            'golden state warriors'     => 'golden st warriors',
            'portland trailblazers'     => 'portland tblazers',
            'oklahoma city thunder'     => 'okc thunder'
        );
        
        $teamKey = strtolower($team);
        
        if(array_key_exists($teamKey, $shortNames)) {
            return $shortNames[$teamKey];
        } else {
            return $team;
        }
        
    }
    
}
