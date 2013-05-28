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

global $download_config;
$download_config = array(
    // fileのダウンロード場所(example:'/tmp')
    "downloadfile_path" => '/tmp',
    // headerファイルの保管場所(example:'/tmp/headers')
    "header_path" => '/tmp/headers',
    // UserAgentの設定
    "user_agent" => 'Mozilla/5.0 (Windows NT 5.1; rv:21.0) Gecko/20130401 Firefox/21.0',
    // downloadの間隔をsleep_time秒数分空ける
    "sleep_time" => 10,
    // ファイルダウンロードのタイムアウト値
    "content_timeout" => 60,
    "service" => array(
        "exploader" => array(
            // mainはトップページアドレス
            "main"   => 'http://www.exploader.net',
            // downloadはdownloadページアドレス
            "download" => 'http://www.exploader.net/download/',
                             ),
        "2dbook"    => array(
            // mainはトップページアドレス
            "main"   => 'http://2dbook.com/',
            // downloadはdownloadページアドレス
            "download" => 'http://2dbook.com/books/',
                             ),
        ),
    );
