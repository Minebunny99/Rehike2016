<?php
use Rehike\ControllerV2\Router;
use Rehike\SimpleFunnel;
use Rehike\ConfigManager\ConfigManager;

if (isset($_GET["enable_polymer"]) && $_GET["enable_polymer"] == "1") {
    SimpleFunnel::funnelCurrentPage(true);
}

if (ConfigManager::getConfigProp("appearance.modernLogo")) {
	Router::funnel([
    "/favicon.ico"
	]);
}

Router::funnel([
    "/api/*",
    "/youtubei/*",
    "/s/*",
    "/embed/*",
    "/yts/*",
    "/subscribe_embed",
    "/login",
    "/logout",
    "/signin",
    "/upload",
    "/t/*",
    "/howyoutubeworks/*",
    "/create_channel",
    "/new",
    "/supported_browsers",
    "/getAccountSwitcherEndpoint",
    "/channel_image_upload/*",
    "/account",
    "/account_notifications",
    "/account_playback",
    "/account_privacy",
    "/account_sharing",
    "/account_billing",
    "/account_advanced",
    "/account_transfer_channel",
    "/features",
    "/testtube",
    "/t/terms",
    "/iframe_api"
]);

Router::redirect([
    "/watch/(*)" => "/watch?v=$1",
    "/shorts/(*)" => "/watch?v=$1",
    "/hashtag/(*)" => "/results?search_query=$1",
    "/feed/what_to_watch/**" => "/",
    "/source/(*)" => function($request) {
        if (isset($request->path[1]))
            return "/attribution?v=" . $request->path[1];
        else
            return "/attribution";
    },
    "/redirect(/|?)*" => function($request) {
        if (isset($request->params->q))
            return urldecode($request->params->q);
    },
    "/feed/library" => "/profile",
    "/subscription_manager" => "/feed/channels",
    "/rehike/settings" => "/rehike/config",
    "/favicon.ico" => "/yts/img/favicon_32-vfl8NGn4k.png",
    "/subscription_center?(*)" => function($request) {
        if ($user = @$request->params->add_user)
            return "/user/$user?sub_confirmation=1";
    }
]);

Router::get([
    "/" => "feed",
    "/feed/**" => "feed",
    "/debug_browse" => "debug_browse",
    "/watch" => "watch",
    "/user/**" => "channel",
    "/channel/**" => "channel",
    "/c/**" => "channel",
    "/live_chat" => "special/get_live_chat",
    "/live_chat_replay" => "special/get_live_chat",
    "/feed_ajax" => "ajax/feed",
    "/results" => "results",
    "/playlist" => "playlist",
    "/oops" => "oops",
    "/forcefatal" => "forcefatal",
    "/all_comments" => "all_comments",
    "/related_ajax" => "ajax/related",
    "/browse_ajax" => "ajax/browse",
    "/addto_ajax" => "ajax/addto",
    "/rehike/version" => "rehike/version",
    "/rehike/static/**" => "rehike/static_router",
    "/share_ajax" => "ajax/share",
    "/attribution" => "attribution",
    "/profile" => "profile",
    "/channel_switcher" => "channel_switcher",
    "/rehike/config" => "rehike/config",
    "/rehike/config/**" => "rehike/config",
    "default" => "channel"
]);

Router::post([
    "/feed_ajax" => "ajax/feed",
    "/browse_ajax" => "ajax/browse",
    "/watch_fragments2_ajax" => "ajax/watch_fragments2",
    "/related_ajax" => "ajax/related",
    "/playlist_video_ajax" => "ajax/playlist_video",
    "/subscription_ajax" => "ajax/subscription",
    "/service_ajax" => "ajax/service",
    "/comment_service_ajax" => "ajax/comment_service",
    "/addto_ajax" => "ajax/addto",
    "/live_events_reminders_ajax" => "ajax/live_events_reminders",
    "/delegate_account_ajax" => "ajax/delegate_account",
    "/rehike/update_config" => "rehike/update_config",
    "default" => "channel"
]);