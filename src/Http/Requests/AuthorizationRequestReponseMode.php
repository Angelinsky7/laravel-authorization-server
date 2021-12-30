<?php

namespace Darkink\AuthorizationServer\Http\Requests;

enum AuthorizationRequestReponseMode : string
{
    case DECISION = 'decision';
    case PERMISSIONS = 'permissions';
    case ANALYSE = 'analyse';
}
