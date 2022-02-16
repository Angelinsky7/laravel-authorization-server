<?php

namespace Darkink\AuthorizationServer\Helpers;

class FlashMessage
{

    public string $message;
    public bool $autoclose;
    public int $duration;
    public FlashMessageSize $size;

    public function __construct(string $message, bool $autoclose = true, int $duration = 3000, FlashMessageSize | string $size = 'basic')
    {
        $this->message = $message;
        $this->autoclose = $autoclose;
        $this->duration = $duration;
        $this->size = is_string($size) ? FlashMessageSize::tryFrom($size) : $size;
    }
}
