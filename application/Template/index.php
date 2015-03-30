<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="the Anthor Simple web interface to manage Redis databases. .">
<link rel="shortcut icon" href="http://o.tiyee.com.cn/favicon.ico" type="image/x-icon"/>

    <title>PHP Redis manage tool. </title>




<link rel="stylesheet" href="/Static/css/pure-min.css">








    <!--[if lte IE 8]>
        <link rel="stylesheet" href="/Static/css/layouts/email-old-ie.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="/Static/css/layouts/email.css">
    <!--<![endif]-->
    <link rel="stylesheet" href="/Static/css/layouts/style.css">


<script src="/Static/js/angular.min.js"></script>
        <script src="/Static/js/angular-route.min.js"></script>
        <script src="/Static/js/app.js"></script>





</head>
<body>






<div id="layout" class="content pure-g"  ng-app="redisAPP">
    <div id="nav" class="pure-u" >
        <a href="#" class="nav-menu-button">Menu</a>

        <div class="nav-inner">
            <button class="primary-button pure-button">Databases</button>


            <div class="pure-menu pure-menu-open">
                <ul>
                    <?php for($i = 0;$i<$databases;$i++):?>
                    <li><a href="?db=<?php echo $i;?>"><?php echo $i;?>
                    <?php if($i == $database):?>

                    <span class="email-count">(<?php echo $dbsize;?>)</span></a></li>
                    <?php endif?>
                    <?php endfor;?>
                    <!-- <li><a href="#">Important</a></li>
                    <li><a href="#">Sent</a></li>
                    <li><a href="#">Drafts</a></li>
                    <li><a href="#">Trash</a></li> -->
                    <!-- <li class="pure-menu-heading">Sign</li> -->
                    <li><a href="javascript:;"><span class="email-label-personal string"></span>string</a></li>
                    <li><a href="javascript:;"><span class="email-label-work list"></span>list</a></li>
                    <li><a href="javascript:;"><span class="email-label-travel set"></span>set</a></li>
                    <li><a href="javascript:;"><span class="email-label-travel zset"></span>zset</a></li>
                    <li><a href="javascript:;"><span class="email-label-travel hash"></span>hash</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div id="list" class="pure-u-1" ng-init="info.db='<?php echo $database;?>';getKeys()"   ng-controller="key_list">
        <div class="email-item email-item-selected pure-form" >


                <input type="text" ng-model="info.query">


              <!--   <button ng-click="search(-1,info.query)"  class="button-success pure-button">Search</button> -->

        </div>
        <div  class="email-item email-item-selected pure-form">


                    <button ng-click="location(-1)" class="button-warning pure-button">Reset</button>
                    <button ng-click="location(info.it)" class="button-secondary pure-button">Next Scan</button>
        </div>

        <div class="email-item email-item-selected pure-g" ng-repeat="key in info.keys | filter:{'key':info.query} " data-it="{{info.it}}">
            <div class="pure-u">
                <!-- <img class="email-avatar" alt="Tilo Mitra&#x27;s avatar" height="64" width="64" src="/Static/img/common/tilo-avatar.png"> -->
                <span class="email-label-personal  {{key.type}}"> </span>
            </div>

            <div class="pure-u-3-4">
                <!-- <h5 class="email-name"></h5> -->
                <h4 class="email-subject"><a title="{{key.type}}" href="#/{{key.type}}/{{key.key}}">{{key.key}}</a></h4>
                <!-- <p class="email-desc">
                    Hey, I just wanted to check in with you from Toronto. I got here earlier today.
                </p> -->
            </div>
        </div>


        <!-- <div class="email-item email-item-unread pure-g">
            <div class="pure-u">
                <img class="email-avatar" alt="Eric Ferraiuolo&#x27;s avatar" height="64" width="64" src="/Static/img/common/ericf-avatar.png">
            </div>

            <div class="pure-u-3-4">
                <h5 class="email-name">Eric Ferraiuolo</h5>
                <h4 class="email-subject">Re: Pull Requests</h4>
                <p class="email-desc">
                    Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.
                </p>
            </div>
        </div>

        <div class="email-item email-item-unread pure-g">
            <div class="pure-u">
                <img class="email-avatar" alt="YUI&#x27;s avatar" height="64" width="64" src="/Static/img/common/yui-avatar.png">
            </div>

            <div class="pure-u-3-4">
                <h5 class="email-name">YUI Library</h5>
                <h4 class="email-subject">You have 5 bugs assigned to you</h4>
                <p class="email-desc">
                    Duis aute irure dolor in reprehenderit in voluptate velit essecillum dolore eu fugiat nulla.
                </p>
            </div>
        </div>

        <div class="email-item pure-g">
            <div class="pure-u">
                <img class="email-avatar" alt="Reid Burke&#x27;s avatar" height="64" width="64" src="/Static/img/common/reid-avatar.png">
            </div>

            <div class="pure-u-3-4">
                <h5 class="email-name">Reid Burke</h5>
                <h4 class="email-subject">Re: Design Language</h4>
                <p class="email-desc">
                    Excepteur sint occaecat cupidatat non proident, sunt in culpa.
                </p>
            </div>
        </div>

        <div class="email-item pure-g">
            <div class="pure-u">
                <img class="email-avatar" alt="Andrew Wooldridge&#x27;s avatar" height="64" width="64" src="/Static/img/common/andrew-avatar.png">
            </div>

            <div class="pure-u-3-4">
                <h5 class="email-name">Andrew Wooldridge</h5>
                <h4 class="email-subject">YUI Blog Updates?</h4>
                <p class="email-desc">
                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                </p>
            </div>
        </div>

        <div class="email-item pure-g">
            <div class="pure-u">
                <img class="email-avatar" alt="Yahoo! Finance&#x27;s Avatar" height="64" width="64" src="/Static/img/common/yfinance-avatar.png">
            </div>

            <div class="pure-u-3-4">
                <h5 class="email-name">Yahoo! Finance</h5>
                <h4 class="email-subject">How to protect your finances from winter storms</h4>
                <p class="email-desc">
                    Mauris tempor mi vitae sem aliquet pharetra. Fusce in dui purus, nec malesuada mauris.
                </p>
            </div>
        </div>

        <div class="email-item pure-g">
            <div class="pure-u">
                <img class="email-avatar" alt="Yahoo! News&#x27; avatar" height="64" width="64" src="/Static/img/common/ynews-avatar.png">
            </div>

            <div class="pure-u-3-4">
                <h5 class="email-name">Yahoo! News</h5>
                <h4 class="email-subject">Summary for April 3rd, 2012</h4>
                <p class="email-desc">
                    We found 10 news articles that you may like.
                </p>
            </div>
        </div> -->
    </div>

    <div id="main" class="pure-u-1"  ng-view>

    </div>
</div>







</body>
</html>
