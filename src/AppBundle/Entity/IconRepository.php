<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * IconRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IconRepository extends EntityRepository
{
    const RETURN_LIMIT = 50;

    public function searchByName($searchTerm) {
        $conn = $this->getEntityManager()->getConnection();

        $query     = "SELECT * from icon WHERE `name` LIKE :search_term LIMIT 50";
        $statement = $conn->prepare( $query );

        $statement->bindValue('search_term', '%' . $searchTerm . '%');

        $statement->execute();

        $icons = $statement->fetchAll();

        $return = array();

        foreach($icons as $icon) {
            $the_icon = new Icon();

            $the_icon->setIcon($icon['icon']);
            $the_icon->setName($icon['name']);
            $the_icon->setId((int) $icon['id']);

            $return[] = $icon;
        }

        return $return;
    }
}
