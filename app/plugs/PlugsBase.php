<?php

namespace app\plugs;


abstract class PlugsBase
{
    abstract public function get_config():PlugsConfig;
    abstract public function install();
    abstract public function remove();
    abstract public function init();
}