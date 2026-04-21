<?php
////////////////////// SUPEREMBED PLAYER SCRIPT //////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
////////////////////////// PLAYER SETTINGS ///////////////////////////////////////////////

$player_font = "Poppins";
$player_bg_color = "060608";
$player_font_color = "ffffff";
$player_primary_color = "FF1744";
$player_secondary_color = "FF6D00";
$player_loader = 1;
$preferred_server = 0;
$player_sources_toggle_type = 2;

//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

// Send headers to prevent clickjacking on our own pages
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: frame-ancestors 'self'");

if (isset($_GET['video_id'])) {
    $video_id = $_GET['video_id'];
    $is_tmdb  = isset($_GET['tmdb'])    ? intval($_GET['tmdb'])    : 0;
    $season   = isset($_GET['season'])  ? intval($_GET['season'])  : (isset($_GET['s'])  ? intval($_GET['s'])  : 0);
    $episode  = isset($_GET['episode']) ? intval($_GET['episode']) : (isset($_GET['e'])  ? intval($_GET['e'])  : 0);

    if (!empty(trim($video_id))) {
        $request_url = "https://getsuperembed.link/?video_id=" . urlencode($video_id)
            . "&tmdb=$is_tmdb&season=$season&episode=$episode"
            . "&player_font=$player_font"
            . "&player_bg_color=$player_bg_color"
            . "&player_font_color=$player_font_color"
            . "&player_primary_color=$player_primary_color"
            . "&player_secondary_color=$player_secondary_color"
            . "&player_loader=$player_loader"
            . "&preferred_server=$preferred_server"
            . "&player_sources_toggle_type=$player_sources_toggle_type";

        $player_url = "";

        if (function_exists('curl_version')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $request_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 7);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $player_url = curl_exec($curl);
            curl_close($curl);
        } else {
            $player_url = @file_get_contents($request_url);
        }

        if (!empty($player_url)) {
            if (strpos($player_url, "https://") !== false) {
                header("Location: $player_url");
                exit;
            } else {
                http_response_code(502);
                echo "<span style='color:red'>$player_url</span>";
            }
        } else {
            http_response_code(504);
            echo "Request server didn't respond";
        }
    } else {
        http_response_code(400);
        echo "Missing video_id";
    }
} else {
    http_response_code(400);
    echo "Missing video_id";
}
?>
