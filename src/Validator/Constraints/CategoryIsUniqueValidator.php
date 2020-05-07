<?php 

namespace App\Validator\Constraints;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CategoryIsUniqueValidator extends ConstraintValidator
{

    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function validate($protocol, Constraint $constraint)
    {
        if (!$protocol instanceof Category) {
            throw new UnexpectedTypeException($constraint, Category::class);
        }

        $existingCategory = $this->categoryRepository->findOneBy(['name' => $protocol->getName(), 'theme' => $protocol->getTheme()]);
        // si une catégorie du même nom existe pour le thème actuel
        if ($existingCategory) {
            // si la catégorie du même nom n'est pas la catégorie actuelle
            if (!($existingCategory->getId() === $protocol->getId())) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('name')
                    ->addViolation()
                ;
            }
        }
    }
}
