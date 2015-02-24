<?php

/**
 * @author a.itsekson
 */

namespace Api\Service;



use Doctrine\ORM\EntityManager;

interface EntityManagerAwareInterface {

    public function setEntityManager(EntityManager $em);

    /**
     * @return EntityManager
     */
    public function getEntityManager();

} 