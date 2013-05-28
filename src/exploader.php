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

class Exploader extends Base_Downloader {

    // $downloader is downloader name.
    public $downloader = 'exploader';

    // initialized
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function init() {
        $this->download_url = '';
        $this->download_key = '';
        $this->content_url = '';
        $this->content_url_mirror = '';
        $this->postfields = array();
        $this->response = '';
        $this->cdata = '';
        return $this;
    }

    // セッション付きURLをセット
    public function setUrl($url = '') {
        $this->download_url = $url;
        return $this;
    }

    // パスワードをセット
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
            if (preg_match('/name=\"exp_password\"/',$response)) {
                // curlを連続で叩くのは良くないので、数秒間間隔をあけてcurlを実行する
                sleep($this->sleep_time);
                return true;
            } else {
                return false;
            }
        } else {
            echo curl_error( $ch );
            exit("ERROR. download_url gets error response code. (Curlでdownload_urlをgetする際にエラーが発生しました。)");
        }
        curl_close($ch);
    }


    // execuse checkPasswrodContent
    public function checkPasswordContent() {
        header( "Content-Type: text/html; Charset=UTF8" );

        //$this->cdata = tempnam(sys_get_temp_dir(),'cookie_');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, preg_replace('/\?session\=.*$/','',$this->download_url));
        // POST
        curl_setopt($ch, CURLOPT_POST, true);
        // POST data
        curl_setopt($ch, CURLOPT_POSTFIELDS,$this->postfields);
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

        $response = curl_exec($ch);

        if ($response) {
            $this->response = $response;
            if (preg_match_all('/http\:\/\/[0-9\.]+\/[0-9a-zA-Z\.\/]+/',$response,$matches,PREG_SET_ORDER)) {
                $this->content_url = $matches[0][0];
                $this->content_url_mirror = $matches[1][0];
                // 数秒間隔をあける
                sleep($this->sleep_time);
            } else {
            echo curl_error( $ch );
            exit("ERROR. It can not be authenticated because the URL is invalid password, or are different. (パスワードが異なっているか、もしくはURLが無効なため認証できません)"."\n");
            }
        } else {
            echo curl_error( $ch );
            exit("ERROR. download_url gets error response code. (Curlでdownload_urlにpasswordを入力する際にエラーが発生しました。)"."\n");
        }
        curl_close($ch);
    }

    /*
     * getContentでcheckLockContentとcheckPasswordContentを実行
     */
    public function getContent() {
        if ($this->download_key) {
            if (self::checkLockContent()) {
                $this->postfields = array(
                       "exp_password" => $this->download_key,
                       "submit" => "ダウンロード",
                                          );
                self::checkPasswordContent();
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

    public function getContentUrlMirror() {
        return $this->content_url_mirror;
    }

    public function getFile() {
        header( "Content-Type: text/html; Charset=UTF8" );

        $file_path = $this->download_path."/archive.zip";
        $fp = fopen($file_path, 'w');
        $headerBuff = fopen($this->header_path, 'w+');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->content_url);
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

        // Rename処理
        if(!curl_errno($ch)) {
            rewind($headerBuff);
            $headers = stream_get_contents($headerBuff);
            if(preg_match('/Content-Disposition: .*filename=\"([^\"]+)\"/', $headers, $matches)) {
                rename($file_path, $this->download_path . "/" .mb_convert_encoding($matches[1],"UTF-8","auto"));
            }
        }
        curl_close($ch);
        unlink($this->cdata);
        unlink($this->header_path);
    }
}
