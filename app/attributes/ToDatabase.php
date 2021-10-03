<?php

namespace app\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ToDatabase
{

    public function __construct(public $db) {}

}