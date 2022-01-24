<?php

namespace Darkink\AuthorizationServer\Models;

enum PolicyEnforcement {
    case Disable,
    case Enforcing,
    case Permissive
}
