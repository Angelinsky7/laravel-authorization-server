<?php

namespace Darkink\AuthorizationServer\Models;

enum PolicyEnforcement : int {
    case None = 0;
    case Disable = 1;
    case Enforcing = 2;
    case Permissive = 3;
}
