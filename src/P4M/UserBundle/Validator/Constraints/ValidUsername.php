<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ValidUsername
 *
 * @author Jona
 */

namespace P4M\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidUsername extends Constraint
{
    public $message = 'The string "%string%" contains an illegal character.';
}
