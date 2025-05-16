<?php

namespace Asmit\ResizedColumn\Setup\Concerns;

use Asmit\ResizedColumn\ResizedColumnPlugin;

trait CanResizedColumn
{
    public static function preserveOnDB(): bool
    {
        if (self::resizedColumnPlugged()) {
            return ResizedColumnPlugin::get()->isPreserveOnDBEnabled();
        }

        return false;
    }
}
