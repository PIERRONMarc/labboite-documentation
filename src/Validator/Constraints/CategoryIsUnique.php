<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CategoryIsUnique extends Constraint
{
    public $message = 'Une catégorie du même nom existe déjà pour ce thème.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
