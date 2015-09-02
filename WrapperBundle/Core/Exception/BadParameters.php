<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Exception;

use Exception;

class BadParameters extends Exception
{
    protected $message = 'Bad parameters : location or content must be defined to construct an eZObjectWrapper';
}