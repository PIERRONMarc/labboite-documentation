<?php 

namespace App\Validator\Constraints;

use App\Entity\Tool;
use App\Repository\ToolRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ToolIsUniqueValidator extends ConstraintValidator
{

    private $toolRepository;

    public function __construct(ToolRepository $toolRepository) {
        $this->toolRepository = $toolRepository;
    }

    public function validate($protocol, Constraint $constraint)
    {
        if (!$protocol instanceof Tool) {
            throw new UnexpectedTypeException($constraint, Tool::class);
        }

        $existingTool = $this->toolRepository->findOneBy(['name' => $protocol->getName(), 'category' => $protocol->getCategory()]);
        // si un outil du même nom existe pour la catégorie actuel
        if ($existingTool) {
            // si l'outil du même nom n'est pas la catégorie actuelle
            if (!($existingTool->getId() === $protocol->getId())) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('name')
                    ->addViolation()
                ;
            }
        }
    }
}
