<?php
namespace P4M\UserBundle\Form\DataTransformer;
 
use Symfony\Component\Form\DataTransformerInterface;
 
class SkillsTransformer implements DataTransformerInterface
{
    private $separator = ',';
    
    /**
      * Transforms the Document's value to a value for the form field
      */
    public function transform($skills)
    {
        if (!$skills) {
            $skills = array(); // default value
        }
 
        return implode($this->separator, $skills); // concatenate the tags to one string
    }
 
    /**
      * Transforms the value the users has typed to a value that suits the field in the Document
      */
    public function reverseTransform($skills)
    {
        if (!$skills) {
            $skills = ''; // default
        }
 
        return array_filter(array_map('trim', explode($this->separator, $skills)));
        // 1. Split the string with commas
        // 2. Remove whitespaces around the tags
        // 3. Remove empty elements (like in "tag1,tag2, ,,tag3,tag4")
    }
}