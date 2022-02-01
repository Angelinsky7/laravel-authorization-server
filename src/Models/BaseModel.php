<?php

namespace Darkink\AuthorizationServer\Models;

use Darkink\AuthorizationServer\Traits\HasSearchable;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasSearchable;



}
