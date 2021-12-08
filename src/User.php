<?php

namespace Hu\Petrik;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $visible = ["id", "email"];
}
