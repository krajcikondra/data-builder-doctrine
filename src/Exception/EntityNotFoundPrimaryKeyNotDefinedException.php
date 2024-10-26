<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\Exception;

use Exception;

final class EntityNotFoundPrimaryKeyNotDefinedException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
