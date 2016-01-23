<?php

if(Director::isDev()) {
    Config::inst()->update('BaseRestController', 'https_only', false);
}