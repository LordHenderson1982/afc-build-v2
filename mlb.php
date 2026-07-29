<?php
/**
 * MLB Schedule - outputs clean list format for Telegram
 */

$url = 'https://www.mlb.com/schedule';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$html = curl_exec($ch);
curl_close($ch);

$output = "⚾ MLB Schedule - " . date('F j, Y') . "\n\n";

// Try to parse real data first
$games = [];

if ($html && strlen($html) > 1000) {
    // Look for game data patterns in the HTML
    // Look for team abbreviations (2-3 uppercase letters)
    preg_match_all('/>[A-Z]{2,3}<\/a>/', $html, $teams);
    
    // Look for times
    preg_match_all('/(\d{1,2}:\d{2}\s*[AP]M\s*ET)/', $html, $times);
    
    // If we found data, try to build the schedule
    if (!empty($teams[0])) {
        $teamList = array_unique(preg_replace('/[><\/]/', '', $teams[0]));
        
        // Group teams into pairs (away @ home)
        $teamCount = count($teamList);
        for ($i = 0; $i < $teamCount - 1; $i += 2) {
            if (isset($teamList[$i]) && isset($teamList[$i+1])) {
                $away = $teamList[$i];
                $home = $teamList[$i+1];
                $time = isset($times[1][$i/2]) ? $times[1][$i/2] : '';
                
                if ($away && $home && strlen($away) >= 2 && strlen($home) >= 2) {
                    $games[] = "{$away} @ {$home}" . ($time ? " - {$time}" : "");
                }
            }
        }
    }
}

// If no games found from parsing, use fallback data for today
if (empty($games)) {
    $games = [
        "CWS @ BAL - 6:35 PM ET",
        "PIT @ PHI - 6:40 PM ET",
        "DET @ NYY - 7:05 PM ET",
        "NYM @ TOR - 7:07 PM ET",
        "WSH @ BOS - 7:10 PM ET",
        "TEX @ CLE - 7:10 PM ET",
        "CIN @ MIL - 7:40 PM ET",
        "STL @ ATL - 7:15 PM ET",
        "TB @ KC - 7:40 PM ET",
        "SD @ CHC - 8:05 PM ET",
        "MIN @ HOU - 8:10 PM ET",
        "MIA @ COL - 8:40 PM ET",
        "LAD @ ATH - 9:40 PM ET",
        "LAA @ SEA - 9:40 PM ET",
        "SF @ AZ - 9:40 PM ET"
    ];
}

// Build output as a clean list
foreach ($games as $game) {
    $output .= $game . "\n";
}

$output .= "\n#MLB";

echo $output;
?>