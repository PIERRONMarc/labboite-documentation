<?php 

namespace App\Validator\Constraints;

use App\Entity\Category;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Make sure a category is unique in his theme
 */
class CategoryIsUniqueValidator extends ConstraintValidator
{

    /**
     * For categry request
     *
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function validate($protocol, Constraint $constraint)
    {
        if (!$protocol instanceof Category) {
            throw new UnexpectedTypeException($constraint, Category::class);
        }

        $slug = Urlizer::urlize($protocol->getName());
        $existingCategory = $this->categoryRepository->findOneBy(['slug' => $slug, 'theme' => $protocol->getTheme()]);
        // if a category with the same name already exist for this theme
        if ($existingCategory) {
            // if the same name category is not the current category
            if (!($existingCategory->getId() === $protocol->getId())) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('name')
                    ->addViolation()
                ;
            }
        }
    }
}
