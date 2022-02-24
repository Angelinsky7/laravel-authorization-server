<?php

namespace Darkink\AuthorizationServer\Models;

enum PolicyLogic : int {
    case None = 0;
    case Negative = 1;
    case Positive = 2;
}
