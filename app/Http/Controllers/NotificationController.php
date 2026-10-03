<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelFCM\Message\Topics;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use App\Mobileuser;

class NotificationController extends Controller
{
    public function individualNotificationSend($fcmid, $title, $flag, $content)
    {

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();

        $notificationBuilder->setTitle($title)
            ->setBody($content['Message']);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'title' => $title, 'msg' => $content, 'flag' => $flag,
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($fcmid, $option, $notification = null, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
    }

    public function sendGroupNotification($flag, $title, $content)
    {



        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle($title)
            ->setBody($content['Message']);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'title' => $title, 'msg' => $content, 'flag' => $flag,
        ]);
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $topic = new Topics();
        $topic->topic('euro_paints_soa');

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);

        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
        if ($topicResponse->isSuccess() > 0) {
            return response()->json(['result' => 'success', 'message' => 'FCM Notification successfully sent.'], 200);
        } else {
            return response()->json(['result' => 'error', 'message' => 'FCM Notification was not sent'], 200);
        }
    }




    public function sendMessageMulti($fcmid, $title, $flag, $content)
    {

        // $validator = Validator::make($request->all(), [
        //             'fcmid' => 'required',
        //             'title' => 'required|max:255',
        //             'body' => 'required|max:500',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(["message" => "error", "data" => $validator->messages()], 422);
        // } else {

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle($title)
            ->setBody($content);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'title' => $title, 'msg' => $content, 'flag' => $flag
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // You must change it to get your tokens
        // $tokens = explode(',', $request->fcmid);
        // $fcmid = Mobileuser::pluck('fcm_id')->toArray();

        $downstreamResponse = FCM::sendTo($fcmid, $option, $notification = null, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();
        // return Array (key:token, value:errror) - in production you should remove from your database the tokens
        if ($downstreamResponse->numberSuccess() > 0) {
            return response()->json(['result' => 'success', 'message' => 'FCM Notification successfully sent.'], 200);
        } else {
            return response()->json(['result' => 'error', 'message' => 'FCM Notification was not sent'], 200);
        }
    }

    public function individualNotificationSendTint($fcmid, $title, $flag, $content)
    {

        config(['fcm.http.server_key' => 'AAAAfrOVF2U:APA91bFhOSruA-Whd7-2vMpo2osmXb0osYQdJOx4-6mtZ-XsV7IUz-PTJdO3GEN_NK4VPqlORczS0zRGPpo6mhSbaacj8wpzaQnyXsSUmEd8v6nrEJwgG3_SSfMqCCYLbRMC8VcHPPxq']);
        config(['fcm.http.sender_id' => '544178771813']);

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();

        $notificationBuilder->setTitle($title)
            ->setBody($content['Message']);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'title' => $title, 'msg' => $content, 'flag' => $flag,
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($fcmid, $option, $notification = null, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
    }
}
