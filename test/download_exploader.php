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

// デフォルトの設定はconfig.php内で設定する
require_once '../src/config.php';
require_once '../src/exploader.php';

$download_url = '';
$download_password = '';

$test = new Exploader($download_config);

/*
 * init: 初期化
 * setUrl: セッション付きurlの設定
 * setPassword: パスワードの設定
 */
$test->init()->setUrl($download_url)->setPassword($download_password);

// download実行
$test->getContent()->getFile();

// URLの出力
//echo $test->getUrl()."\n";
// htmlレスポンスの取得
//echo $test->getResponse();
// ダウンロードURLを取得
//echo $test->content_url;
