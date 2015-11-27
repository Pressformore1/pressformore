<?php

namespace P4M\UserBundle\Form\DataTransformer;

use P4M\UserBundle\Entity\Invitation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms an Invitation to an invitation code.
 */
class InvitationToCodeTransformer implements DataTransformerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Invitation) {
            throw new UnexpectedTypeException($value, 'P4M\UserBundle\Entity\Invitation');
        }

        return $value->getCode();
    }

//    public function reverseTransform($value)
//    {
//        if (null === $value || '' === $value) {
//            return null;
//        }
//
//        if (!is_string($value)) {
//            throw new UnexpectedTypeException($value, 'string');
//        }
//
//        return $this->entityManager
//            ->getRepository('P4M\UserBundle\Entity\Invitation')
//            ->findOneBy(array(
//                'code' => $value,
//                'user' => null,
//            ));
//    }
    
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }
        
        $invitation = $this->entityManager
            ->getRepository('P4M\UserBundle\Entity\Invitation')
            ->findOneBy(array(
                'code' => $value,
            ));
        
        if($this->entityManager->getRepository('P4M\UserBundle\Entity\User')->findOneBy(array("invitation" => $invitation))){
            return null;
        }
        
//        die('breakPoint');
        return $invitation;
    }
}