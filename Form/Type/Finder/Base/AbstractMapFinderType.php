<?php
/**
 * Maps.
 *
 * @copyright Krator (TdM)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Krator <torredemarfil@heroesofmightandmagic.es>.
 * @link https://www.heroesofmightandmagic.es
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.1.0 (https://modulestudio.de).
 */

namespace TdM\MapsModule\Form\Type\Finder\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * Map finder form type base class.
 */
abstract class AbstractMapFinderType extends AbstractType
{
    use TranslatorTrait;

    /**
     * MapFinderType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('objectType', HiddenType::class, [
                'data' => $options['object_type']
            ])
            ->add('editor', HiddenType::class, [
                'data' => $options['editor_name']
            ])
        ;

        $this->addImageFields($builder, $options);
        $this->addPasteAsField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addSearchField($builder, $options);

        $builder
            ->add('update', SubmitType::class, [
                'label' => $this->__('Change selection'),
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', SubmitType::class, [
                'label' => $this->__('Cancel'),
                'icon' => 'fa-times',
                'attr' => [
                    'class' => 'btn btn-default',
                    'formnovalidate' => 'formnovalidate'
                ]
            ])
        ;
    }

    /**
     * Adds fields for image insertion options.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addImageFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('onlyImages', CheckboxType::class, [
            'label' => $this->__('Only images'),
            'empty_data' => false,
            'help' => $this->__('Enable this option to insert images'),
            'required' => false
        ]);
        $builder->add('imageField', ChoiceType::class, [
            'label' => $this->__('Image field'),
            'empty_data' => 'foreground',
            'help' => $this->__('You can switch between different image fields'),
            'choices' => [
                $this->__('Foreground') => 'foreground',
                $this->__('Underground') => 'underground'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a "paste as" field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addPasteAsField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pasteAs', ChoiceType::class, [
            'label' => $this->__('Paste as') . ':',
            'empty_data' => 1,
            'choices' => [
                $this->__('Relative link to the map') => 1,
                $this->__('Absolute url to the map') => 2,
                $this->__('ID of map') => 3,
                $this->__('Relative link to the image') => 6,
                $this->__('Image') => 7,
                $this->__('Image with relative link to the map') => 8,
                $this->__('Image with absolute url to the map') => 9
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds sorting fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSortingFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'label' => $this->__('Sort by') . ':',
                'empty_data' => '',
                'choices' => [
                    $this->__('Workflow state') => 'workflowState',
                    $this->__('Name') => 'name',
                    $this->__('Author') => 'author',
                    $this->__('Map file') => 'mapFile',
                    $this->__('Test state') => 'testState',
                    $this->__('Game') => 'game',
                    $this->__('Size map') => 'sizeMap',
                    $this->__('B underground') => 'bUnderground',
                    $this->__('Language map') => 'languageMap',
                    $this->__('Create dat') => 'createDat',
                    $this->__('Version map') => 'versionMap',
                    $this->__('Difficulty') => 'difficulty',
                    $this->__('N humans') => 'nHumans',
                    $this->__('N players') => 'nPlayers',
                    $this->__('Game type') => 'gameType',
                    $this->__('Map style') => 'mapStyle',
                    $this->__('Description') => 'description',
                    $this->__('Foreground') => 'foreground',
                    $this->__('Underground') => 'underground',
                    $this->__('Score rev') => 'scoreRev',
                    $this->__('N score rev') => 'nScoreRev',
                    $this->__('N downloads') => 'nDownloads',
                    $this->__('Creation date') => 'createdDate',
                    $this->__('Creator') => 'createdBy',
                    $this->__('Update date') => 'updatedDate',
                    $this->__('Updater') => 'updatedBy'
                ],
                'multiple' => false,
                'expanded' => false
            ])
            ->add('sortdir', ChoiceType::class, [
                'label' => $this->__('Sort direction') . ':',
                'empty_data' => 'asc',
                'choices' => [
                    $this->__('Ascending') => 'asc',
                    $this->__('Descending') => 'desc'
                ],
                'multiple' => false,
                'expanded' => false
            ])
        ;
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAmountField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('num', ChoiceType::class, [
            'label' => $this->__('Page size') . ':',
            'empty_data' => 20,
            'attr' => [
                'class' => 'text-right'
            ],
            'choices' => [
                $this->__('5') => 5,
                $this->__('10') => 10,
                $this->__('15') => 15,
                $this->__('20') => 20,
                $this->__('30') => 30,
                $this->__('50') => 50,
                $this->__('100') => 100
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a search field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSearchField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q', SearchType::class, [
            'label' => $this->__('Search for') . ':',
            'required' => false,
            'attr' => [
                'maxlength' => 255
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'tdmmapsmodule_mapfinder';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'object_type' => 'map',
                'editor_name' => 'ckeditor'
            ])
            ->setRequired(['object_type', 'editor_name'])
            ->setAllowedTypes('object_type', 'string')
            ->setAllowedTypes('editor_name', 'string')
            ->setAllowedValues('editor_name', ['ckeditor', 'quill', 'summernote', 'tinymce'])
        ;
    }
}