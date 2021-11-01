<?php

namespace system\interfaces;

interface QueryBuilderInterface {
    
    const DB_OR = ' OR';
    const DB_AND = ' AND';
    const LEFT_QUOTE = ' (';
    const RIGHT_QUOTE = ')';
    const DESC = ' DESC';
    const ASC = ' ASC';
    const INNER = '';
    const LEFT = 'LEFT ';
    const RIGHT = 'RIGHT ';
    const OUTER = 'OUTER ';
    const COUNT = 'COUNT()';
    
}
