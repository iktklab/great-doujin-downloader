<?php
/*
 * Copyright 2013 Iktklab Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License atan *
 *     http://www.apache.org/licenses/LICENSE-2.0 *
 * Unless required by applicable law or agreed to in writing, software * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and * limitations under the License.
 */
require_once 'base_downloader.php';

class Down2dbook extends Base_Downloader {

    // $downloader is downloader name.
    public $downloader = '2dbook';

    // construct
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->content_url = 'http://2dbook.com/downloads';
    }

    // 複数回実行するときに初期化をここでする
    public function init() {
        $this->download_url = '';
        $this->download_key = '';
        $this->download_key_exist = true;
        $this->download_url_id = '';
        $this->download_url_session = '';
        $this->postfields = array();
        $this->response = '';
        $this->cdata = '';
        return $this;
    }

    // ここでURLをセット
    public function setUrl($url = '') {
        $this->download_url = $url;
        return $this;
    }

    // ここでパスワードをセット
    public function setPassword($password = '') {
        $this->download_key = $password;
        return $this;
    }

    // execuse checkLockContent
    public function checkLockContent() {
        header( "Content-Type: text/html; Charset=UTF8" );

        $this->cdata = tempnam(sys_get_temp_dir(),'cookie_');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->download_url);
        // UserAgentの設定
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        // 結果を文字列として返す
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // cookieに書き込む
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cdata);
        // redirect先も取得
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);

        if ($response) {
            if (preg_match('/name=\"data\[dlkey\]/',$response)) {
                // curlを連続で叩くのは良くないので、数秒間間隔をあけてcurlを実行する
                sleep($this->sleep_time);
                $this->download_key_exist = true;
            } else {
                // curlを連続で叩くのは良くないので、数秒間間隔をあけてcurlを実行する
                sleep($this->sleep_time);
                $this->download_key_exist = false;
            }
            return true;
        } else {
            echo curl_error( $ch );
            return false;
        }
        curl_close($ch);
    }

    public function parseDownloadUrl() {
        preg_match('/http\:\/\/2dbook\.com\/books\/([0-9]+)\/([^\/]+)/',$this->download_url, $matches);
        $this->download_url_id = $matches[1];
        $this->download_url_session = $matches[2];
    }
    /*
     * getContentでcheckLockContentとcheckPasswordContentを実行
     */
    public function getContent() {
        if ($this->download_key) {
            if (self::checkLockContent()) {
                self::parseDownloadUrl();

                $this->postfields = array(
                       "_method"      => 'POST',
                       "data[id]"     => $this->download_url_id,
                       "data[unlock]" => $this->download_url_session,
                       "dl"           => 'dl',
                                          );
                if ($this->download_key_exist) $this->postfields["data[dlkey]"] = $this->download_key;
            } else {
                exit("ERROR. download_url is differ Session or download_url is deleted. (Sessionが異なっているか、コンテンツが削除されているためダウンロードできません。)");
            }
        } else {
            exit("ERROR. download_key is empty. (DL_keyが空です。setPasswordでdl_keyを追加してください。)");
        }
        return $this;
    }

    public function getDownloadUrl() {
        return $this->download_url;
    }

    public function getContentUrl() {
        return $this->content_url;
    }

    public function getFile() {
        header( "Content-Type: text/html; Charset=UTF8" );

        $file_path = $this->download_path."/archive.zip";
        $fp = fopen($file_path, 'w');
        $headerBuff = fopen($this->header_path, 'w+');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->content_url);
        // POST
        curl_setopt($ch, CURLOPT_POST, true);
        // POST data
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this->postfields);
        // Headerを書き込む
        curl_setopt($ch, CURLOPT_WRITEHEADER, $headerBuff);
        // UserAgentの設定
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        // 結果を文字列として返す
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // cookie読み込み
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cdata);
        // redirect先も取得
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // referer先はdownload_urlに
        curl_setopt($ch, CURLOPT_REFERER, $this->download_url);
        // fileをダウンロード
        curl_setopt($ch, CURLOPT_FILE, $fp);
        // fileのダウンロードタイムアウト（秒）
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->content_timeout);
        $response = curl_exec($ch);

        if ($response) {
            $this->response = $response;
        } else {
            echo curl_error( $ch );
            exit("ERROR. download_url gets error response code. (Curlでdownload_urlにpasswordを入力する際にエラーが発生しました。)"."\n");
        }

        // Rename処理
        if(!curl_errno($ch)) {
            rewind($headerBuff);
            $headers = stream_get_contents($headerBuff);
            if(preg_match('/Location: .*\/file\/(.*(zip|lzh|rar))/', $headers, $matches)) {
                rename($file_path, $this->download_path . "/" .mb_convert_encoding($matches[1],"UTF-8","auto"));
            }
        }
        curl_close($ch);
        unlink($this->cdata);
        unlink($this->header_path);
    }
}
