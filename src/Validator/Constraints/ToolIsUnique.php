<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ToolIsUnique extends Constraint
{
    public $message = 'Un item du même nom existe déjà pour ce thème.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
