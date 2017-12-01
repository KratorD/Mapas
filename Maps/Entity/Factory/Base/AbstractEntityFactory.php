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

namespace TdM\MapsModule\Entity\Factory\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use TdM\MapsModule\Entity\Factory\EntityInitialiser;
use TdM\MapsModule\Helper\CollectionFilterHelper;

/**
 * Factory class used to create entities and receive entity repositories.
 */
abstract class AbstractEntityFactory
{
    /**
     * @var ObjectManager The object manager to be used for determining the repository
     */
    protected $objectManager;

    /**
     * @var EntityInitialiser The entity initialiser for dynamical application of default values
     */
    protected $entityInitialiser;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * EntityFactory constructor.
     *
     * @param ObjectManager          $objectManager          The object manager to be used for determining the repositories
     * @param EntityInitialiser      $entityInitialiser      The entity initialiser for dynamical application of default values
     * @param CollectionFilterHelper $collectionFilterHelper CollectionFilterHelper service instance
     */
    public function __construct(
        ObjectManager $objectManager,
        EntityInitialiser $entityInitialiser,
        CollectionFilterHelper $collectionFilterHelper)
    {
        $this->objectManager = $objectManager;
        $this->entityInitialiser = $entityInitialiser;
        $this->collectionFilterHelper = $collectionFilterHelper;
    }

    /**
     * Returns a repository for a given object type.
     *
     * @param string $objectType Name of desired entity type
     *
     * @return EntityRepository The repository responsible for the given object type
     */
    public function getRepository($objectType)
    {
        $entityClass = 'TdM\\MapsModule\\Entity\\' . ucfirst($objectType) . 'Entity';

        $repository = $this->objectManager->getRepository($entityClass);
        $repository->setCollectionFilterHelper($this->collectionFilterHelper);

        return $repository;
    }

    /**
     * Creates a new map instance.
     *
     * @return TdM\MapsModule\Entity\mapEntity The newly created entity instance
     */
    public function createMap()
    {
        $entityClass = 'TdM\\MapsModule\\Entity\\MapEntity';

        $entity = new $entityClass();

        $this->entityInitialiser->initMap($entity);

        return $entity;
    }

    /**
     * Returns the identifier field's name for a given object type.
     *
     * @param string $objectType The object type to be treated
     *
     * @return string Primary identifier field name
     */
    public function getIdField($objectType = '')
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException('Invalid object type received.');
        }
        $entityClass = 'TdMMapsModule:' . ucfirst($objectType) . 'Entity';
    
        $meta = $this->getObjectManager()->getClassMetadata($entityClass);
    
        return $meta->getSingleIdentifierFieldName();
    }

    /**
     * Returns the object manager.
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
    
    /**
     * Sets the object manager.
     *
     * @param ObjectManager $objectManager
     *
     * @return void
     */
    public function setObjectManager($objectManager)
    {
        if ($this->objectManager != $objectManager) {
            $this->objectManager = $objectManager;
        }
    }
    

    /**
     * Returns the entity initialiser.
     *
     * @return EntityInitialiser
     */
    public function getEntityInitialiser()
    {
        return $this->entityInitialiser;
    }
    
    /**
     * Sets the entity initialiser.
     *
     * @param EntityInitialiser $entityInitialiser
     *
     * @return void
     */
    public function setEntityInitialiser($entityInitialiser)
    {
        if ($this->entityInitialiser != $entityInitialiser) {
            $this->entityInitialiser = $entityInitialiser;
        }
    }
    
}
