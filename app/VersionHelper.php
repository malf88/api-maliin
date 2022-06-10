<?php

namespace App;
use MCStreetguy\ComposerParser\Factory as ComposerParser;
class VersionHelper
{
    public static function version():string
    {
        $composer = ComposerParser::parseComposerJson(base_path().'/composer.json');
        return $composer->getVersion();
    }
}
