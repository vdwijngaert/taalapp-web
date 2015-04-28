<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * QuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuestionRepository extends EntityRepository
{
    public function findByUser( User $user )
    {
        $questions = array();

        $connection = $this->getEntityManager()->getConnection();

        $query = "SELECT q.* FROM question q join category cat on(q.category_id = cat.id) where cat.status = 1 and q.status = 1 and cat.user_id = :user_id order by q.id asc";

        $statement = $connection->prepare( $query );
        $statement->bindValue( 'user_id', $user->getId() );

        $statement->execute();

        $quests = $statement->fetchAll();

        foreach ($quests as $quest) {
            $newQuestion = new \StdClass();

            $newQuestion->id       = $quest['id'];
            $newQuestion->question = $quest['question'];
            $newQuestion->category = $quest['category_id'];
            $newQuestion->status   = $quest['status'];
            $newQuestion->updated  = $quest['updated'];

            $questions[] = $newQuestion;
        }

        return $questions;
    }

    public function findByUserAfter( User $user, \DateTime $date )
    {
        $questions = array();

        $connection = $this->getEntityManager()->getConnection();

        $query = "SELECT q.* FROM question q join category cat on(q.category_id = cat.id) where q.updated > :last_update and cat.user_id = :user_id order by q.id asc";

        $statement = $connection->prepare( $query );
        $statement->bindValue( 'last_update', $date->format('Y-m-d H:i:s') );
        $statement->bindValue( 'user_id', $user->getId() );

        $statement->execute();

        $quests = $statement->fetchAll();

        foreach ($quests as $quest) {
            $newQuestion = new \StdClass();

            $newQuestion->id       = $quest['id'];
            $newQuestion->question = $quest['question'];
            $newQuestion->category = $quest['category_id'];
            $newQuestion->status   = $quest['status'];
            $newQuestion->updated  = $quest['updated'];

            $questions[] = $newQuestion;
        }

        return $questions;
    }
}
