<?php

namespace Darkink\AuthorizationServer\Models;

enum DecisionStrategy {
    case Unanimous,
    case Affirmative,
    case Consensus
}
