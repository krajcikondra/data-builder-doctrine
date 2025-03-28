<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\Exception;

use Exception;

final class EntityNotFoundNoPrimaryKeyException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
