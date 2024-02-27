<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Etablissement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EtablissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        /*
            ->add('NomEtablissement', TextType::class, [
                'label' => 'Nom de l\'établissement'
            ])
            ->add('description', ChoiceType::class, [
                'label' => 'Description',
                'choices' => [
                    'Hôtel' => 'Hotel',
                    'Hopital' => 'Hopital',
                    'Ehpad' => 'ehpad',
                    'Camping' => 'camping',
                    'Autres' => 'Autres',
                ]
            ])*/
            ->add('nom_etablissement')
            ->add('description')
            ->add('prenom')    
            ->add('nom')    
            ->add('genre')    
            ->add('ville')    
            ->add('pays')    
            ->add('logo')      
            ->add('nbrs_chambre') 
            ->add('active')  
            ->add('addresse')         
            ->add('msgap')    
            ->add('msginternet')  
            ->add('msgbienvenu')  
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etablissement::class,
        ]);
    }
}
