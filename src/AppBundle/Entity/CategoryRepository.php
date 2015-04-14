<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function getCategories( Category $parent = null, User $user )
    {
        $criteria = array( 'user' => $user );

        if($parent !== null) {
            $criteria['parent'] = $parent;
        }

        return $this->findBy( $criteria, array('name' => 'ASC'));
    }
}
