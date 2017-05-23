<?php

namespace OC\PlatformBundle\Form;

use OC\PlatformBundle\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pattern = null;

        $builder
            ->add('date',           DateTimeType::class)
            ->add('author',         TextType::class)
            ->add('authorMail',     EmailType::class )
            ->add('title',          TextType::class)
            ->add('image',          ImageType::class)
            ->add('content',        TextareaType::class )
            ->add('finderProfil',   TextareaType::class)
            ->add('save',           SubmitType::class)
            ->add('categories',     EntityType::class,
                array(
                    "class"         =>'OCPlatformBundle:Category',
                    "choice_label"  => 'name',
                    "expanded"      => true,
                    'multiple'      => true

                ))

        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event){
                $advert = $event->getData();

                if(null === $advert){
                    return;
                }

                if(!$advert->getPublished() || null === $advert->getID()){
                    $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                } else {
                    $event->getForm()->remove('published');
                }
            }

        );
    }



    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }


}
