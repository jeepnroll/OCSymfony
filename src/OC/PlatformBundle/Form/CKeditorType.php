<?php
/**
 * Created by PhpStorm.
 * User: Jeepnroll
 * Date: 23/05/2017
 * Time: 16:30
 */

namespace OC\PlatformBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CKeditorType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            "attr" => array( "class" => 'ckeditor')
        ));
    }

    public function getParent()
    {
        return TextareaType::class;
    }

}