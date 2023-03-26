<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;
use \Rehike\Model\Common\Subscription\MSubscriptionPreferencesOverlay;

/**
 * Controller for subscription actions.
 * 
 * This includes subscribing, unsubscribing, and getting subscription
 * preferences.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController {
    public $useTemplate = false;
    public $template = "ajax/subscription/get_subscription_preferences_overlay";

    public $ytdata;

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        switch ($action) {
            case "create_subscription_to_channel":
                $ytdata = self::createSubscriptionToChannel();
                break;
            case "remove_subscriptions":
                $ytdata = self::removeSubscriptions();
                break;
            case "get_subscription_preferences_overlay":
                $this->useTemplate = true;
                self::getSubscriptionPreferencesOverlay($yt, $request);
                break;
            default:
                self::error();
                break;
        }

        if (!$this->useTemplate) {
            if (is_null($ytdata)) self::error();

            if (!isset($ytdata->error)) {
                http_response_code(200);
                echo json_encode((object) [
                    "response" => "SUCCESS"
                ]);
            } else self::error();
        }
    }

    /**
     * Create a subscription to a channel.
     *
     * @param object          $yt      Template data.
     * @param RequestMetadata $request Request data.
     */
    private static function createSubscriptionToChannel(): Promise {
        return new Promise(function ($resolve) {
            Network::innertubeRequest(
                action: "subscription/subscribe",
                body: [
                    "channelIds" => [
                        $_GET["c"] ?? null
                    ],
                    "params" => $_POST["params"] ?? null
                ]
            )->then(function ($response) use ($resolve) {
                $resolve( $response->getJson() );
            });
        });
    }

    /**
     * Remove a subscription from a channel.
     * 
     * @param object          $yt      Template data.
     * @param RequestMetadata $request Request data.
     */
    private static function removeSubscriptions(): Promise {
        return new Promise(function ($resolve) {
            Network::innertubeRequest(
                action: "subscription/unsubscribe",
                body: [
                    "channelIds" => [
                        $_GET["c"] ?? null
                    ]
                ]
            )->then(function ($response) use ($resolve) {
                $resolve( $response->getJson() );
            });
        });
    }

    /**
     * Get the subscription preferences overlay.
     * 
     * @param object           $yt       Template data.
     * @param RequestMetadata  $request  Request data.
     */
    private static function getSubscriptionPreferencesOverlay(&$yt, $request) {
        $response = Request::innertubeRequest("browse", (object) [
            "browseId" => $_POST["c"] ?? ""
        ]);
        $ytdata = json_decode($response);
        $header = $ytdata->header->c4TabbedHeaderRenderer ?? null;
        $yt->page = new MSubscriptionPreferencesOverlay([
            "title" => $header->title ?? "",
            // Make sure to turn on word wrap @_@
            "options" => $header->subscribeButton->subscribeButtonRenderer->notificationPreferenceButton->subscriptionNotificationToggleButtonRenderer->command->commandExecutorCommand->commands[0]->openPopupAction->popup->menuPopupRenderer->items ?? []
        ]);
    }
};