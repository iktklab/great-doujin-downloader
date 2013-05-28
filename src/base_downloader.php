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

class Base_Downloader {

    public $download_file;

    public $downlaod_path;

    // $main_url is based url.
    public $main_url;

    // $download_url is download form url.
    public $download_url;

    public $download_url_session;

    public $download_url_id;

    public $header_path;

    public $content_url;

    public $content_url_mirror;

    public $content_timeout;
    //If we want to download files, you should use password. Its password is $download_key.
    public $download_key;

    public $download_key_exist;

    public $user_agent = '';

    public $sleep_time;

    public $response;

    public $cdata;

    public function __construct($config){

        $this->user_agent      = $config['user_agent'];
        $this->header_path     = $config['header_path'];
        $this->download_path   = $config['downloadfile_path'];
        $this->sleep_time      = $config['sleep_time'];
        $this->content_timeout = $config['content_timeout'];

        $downloader           = $this->downloader;
        $this->main_url       = $config['service'][$downloader]['main'];
    }

    public function getResponse() {
        return $this->response;
    }
}
