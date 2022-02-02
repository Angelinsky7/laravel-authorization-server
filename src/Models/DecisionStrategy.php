<?php

namespace Darkink\AuthorizationServer\Models;

enum DecisionStrategy : int {
    case None = 0;
    case Unanimous = 1;
    case Affirmative = 2;
    case Consensus = 3;
}
