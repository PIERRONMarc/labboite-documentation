<?php 

namespace App\Validator\Constraints;

use App\Entity\Tool;
use Gedmo\Sluggable\Util\Urlizer;
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

        $slug = Urlizer::urlize($protocol->getName());
        $existingTool = $this->toolRepository->findOneBy(['slug' => $slug, 'category' => $protocol->getCategory()]);
        // if a tool with the same name already exist for this category
        if ($existingTool) {
            // if the same name tool is not the actual tool
            if (!($existingTool->getId() === $protocol->getId())) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('name')
                    ->addViolation()
                ;
            }
        }
    }
}
