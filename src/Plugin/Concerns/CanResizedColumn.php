<?php

namespace Asmit\ResizedColumn\Plugin\Concerns;

trait CanResizedColumn
{
    protected bool $isPreserveOnDbEnabled = false;

    public function preserveOnDB(bool $condition = true): self
    {
        $this->isPreserveOnDbEnabled = $condition;

        return $this;
    }

    public function isPreserveOnDBEnabled(): bool
    {
        return $this->isPreserveOnDbEnabled;
    }
}
