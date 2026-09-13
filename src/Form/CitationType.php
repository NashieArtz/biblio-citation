<?php

namespace App\Form;

use App\Entity\Citation;
use App\Repository\CitationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CitationType extends AbstractType
{
    public function __construct(
        private readonly CitationRepository $citationRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->citationRepository->findDistinctCategories();
        $universes = $this->citationRepository->findDistinctUniverses();
        $tags = $this->citationRepository->findDistinctTags();

        $builder
            ->add('auteur', TypeTextType::class, [
                'label' => 'Auteur',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Citation',
            ])
            ->add('publishing_year', TypeTextType::class, [
                'label' => 'Année de publication',
                'required' => false,
            ])
            ->add('source', TypeTextType::class, [
                'label' => 'Source',
                'required' => false,
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => array_combine($categories, $categories),
                'required' => false,
                'placeholder' => '— Aucune —',
                'invalid_message' => "Cette catégorie n'existe pas dans la liste.",
            ])
            ->add('category_new', TypeTextType::class, [
                'label' => 'Autre catégorie',
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Laisser vide pour utiliser la liste'],
            ])
            ->add('universe', ChoiceType::class, [
                'label' => 'Univers',
                'choices' => array_combine($universes, $universes),
                'required' => false,
                'placeholder' => '— Aucun —',
                'invalid_message' => "Cet univers n'existe pas dans la liste.",
            ])
            ->add('universe_new', TypeTextType::class, [
                'label' => 'Autre univers',
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Laisser vide pour utiliser la liste'],
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags existants',
                'choices' => array_combine($tags, $tags),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['size' => 8],
                'invalid_message' => "Un des tags sélectionnés n'existe pas dans la liste.",
            ])
            ->add('tags_new', TypeTextType::class, [
                'label' => 'Nouveaux tags',
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Séparés par des virgules'],
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $citation = $event->getData();

            if (!$citation instanceof Citation) {
                return;
            }

            $form = $event->getForm();

            $newCategory = trim((string) $form->get('category_new')->getData());
            if ($newCategory !== '') {
                $citation->setCategory($newCategory);
            }

            $newUniverse = trim((string) $form->get('universe_new')->getData());
            if ($newUniverse !== '') {
                $citation->setUniverse($newUniverse);
            }

            $newTags = array_filter(
                array_map('trim', explode(',', (string) $form->get('tags_new')->getData())),
                static fn(string $tag): bool => $tag !== ''
            );

            if ($newTags !== []) {
                $citation->setTags(array_values(array_unique(array_merge($citation->getTags(), $newTags))));
            }
        }, 10);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Citation::class,
        ]);
    }
}
