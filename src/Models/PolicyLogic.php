<?php

namespace Darkink\AuthorizationServer\Models;

enum PolicyLogic : int {
    case Negative = 0;
    case Positive = 1;
}
