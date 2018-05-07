<?php
send_to_slack_attachments ( "<!channel> test", "good" );

/*
 * slackにattatchements付きの通知を送る。
 * @param string $message
 * @param string $color ("danger"=赤、"good"=緑、"warning"=黄。他の色も指定可 ex.#439FE0 =青)
 */
function send_to_slack_attachments($message, $color) {

    // $setting.iniから設定を取得
    $setting = parse_ini_file ( "setting.ini" );
    $webhook_url = $setting ['webhook_url'];

    // attachmentsを作成
    $attachments = make_attachments ( $message, $color );

    // メッセージをjson化
    $message_json = json_encode ( $attachments );

    // payloadの値としてURLエンコード
    $message_post = "payload=" . urlencode ( $message_json );

    // curlで送信する
    //（setting.iniのdry_runがtrueなら、通知を実際には飛ばさない。テスト用）
    if (  !$setting ['dry_run'] ) {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $webhook_url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $message_post );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_exec   ( $ch );
        curl_close  ( $ch );
    } else {
        var_dump ( $attachments );
        var_dump ( $webhook_url );
        var_dump ( $message_post );
    }
}

/*
 * attachmentsを作成する。
 * @param string $message
 * @param string $color
 * @return array $attatchements
 */
function make_attachments($message, $color) {
    $attachments = array (
            "attachments" => array (
                    array (
                            // "title" => "checkCalendar",
                            "text" => $message,
                            "color" => $color,
                            "ts" => time () // unix timestamp
                    )

            )
    );

    return $attachments;
}
