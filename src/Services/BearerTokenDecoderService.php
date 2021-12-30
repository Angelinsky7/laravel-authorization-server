<?php

namespace Darkink\AuthorizationServer\Services;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class BearerTokenDecoderService
{
    protected $parser;

    public function __construct()
    {
        $decoder = new JoseEncoder();
        $this->parser = new Parser($decoder);
    }

    public function parse(string | null $bearerToken)
    {
        if (!$bearerToken) {
            return null;
        }
        return $this->parser->parse($bearerToken);
    }
}
