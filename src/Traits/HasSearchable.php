<?php

namespace Darkink\AuthorizationServer\Traits;

trait HasSearchable
{
    protected $searchable = [];

    public function getSearchable()
    {
        return $this->searchable;
    }
}
