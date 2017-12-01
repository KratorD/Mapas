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

namespace TdM\MapsModule\Controller\Base;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Response\PlainResponse;

/**
 * Controller for external calls base class.
 */
abstract class AbstractExternalController extends AbstractController
{
    /**
     * Displays one item of a certain object type using a separate template for external usages.
     *
     * @param Request $request     The current request
     * @param string  $objectType  The currently treated object type
     * @param int     $id          Identifier of the entity to be shown
     * @param string  $source      Source of this call (contentType or scribite)
     * @param string  $displayMode Display mode (link or embed)
     *
     * @return string Desired data output
     */
    public function displayAction(Request $request, $objectType, $id, $source, $displayMode)
    {
        $controllerHelper = $this->get('tdm_maps_module.controller_helper');
        $contextArgs = ['controller' => 'external', 'action' => 'display'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $contextArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $contextArgs);
        }
        
        $component = 'TdMMapsModule:' . ucfirst($objectType) . ':';
        if (!$this->hasPermission($component, $id . '::', ACCESS_READ)) {
            return '';
        }
        
        $entityFactory = $this->get('tdm_maps_module.entity_factory');
        $repository = $entityFactory->getRepository($objectType);
        
        // assign object data fetched from the database
        $entity = $repository->selectById($id);
        if (null === $entity) {
            return new Response($this->__('No such item.'));
        }
        
        $template = $request->query->has('template') ? $request->query->get('template', null) : null;
        if (null === $template || $template == '') {
            $template = 'display.html.twig';
        }
        
        $templateParameters = [
            'objectType' => $objectType,
            'source' => $source,
            $objectType => $entity,
            'displayMode' => $displayMode
        ];
        
        $contextArgs = ['controller' => 'external', 'action' => 'display'];
        $templateParameters = $this->get('tdm_maps_module.controller_helper')->addTemplateParameters($objectType, $templateParameters, 'controllerAction', $contextArgs);
        
        return $this->render('@TdMMapsModule/External/' . ucfirst($objectType) . '/' . $template, $templateParameters);
    }
    
    /**
     * Popup selector for Scribite plugins.
     * Finds items of a certain object type.
     *
     * @param Request $request    The current request
     * @param string  $objectType The object type
     * @param string  $editor     Name of used Scribite editor
     * @param string  $sort       Sorting field
     * @param string  $sortdir    Sorting direction
     * @param int     $pos        Current pager position
     * @param int     $num        Amount of entries to display
     *
     * @return output The external item finder page
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function finderAction(Request $request, $objectType, $editor, $sort, $sortdir, $pos = 1, $num = 0)
    {
        $assetHelper = $this->get('zikula_core.common.theme.asset_helper');
        $cssAssetBag = $this->get('zikula_core.common.theme.assets_css');
        $cssAssetBag->add($assetHelper->resolve('@TdMMapsModule:css/style.css'));
        
        $activatedObjectTypes = $this->getVar('enabledFinderTypes', []);
        if (!in_array($objectType, $activatedObjectTypes)) {
            if (!count($activatedObjectTypes)) {
                throw new AccessDeniedException();
            }
        
            // redirect to first valid object type
            $redirectUrl = $this->get('router')->generate('tdmmapsmodule_external_finder', ['objectType' => array_shift($activatedObjectTypes), 'editor' => $editor]);
        
            return new RedirectResponse($redirectUrl);
        }
        
        if (!$this->hasPermission('TdMMapsModule:' . ucfirst($objectType) . ':', '::', ACCESS_COMMENT)) {
            throw new AccessDeniedException();
        }
        
        if (empty($editor) || !in_array($editor, ['ckeditor', 'quill', 'summernote', 'tinymce'])) {
            return new Response($this->__('Error: Invalid editor context given for external controller action.'));
        }
        
        $repository = $this->get('tdm_maps_module.entity_factory')->getRepository($objectType);
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }
        
        $sdir = strtolower($sortdir);
        if ($sdir != 'asc' && $sdir != 'desc') {
            $sdir = 'asc';
        }
        
        // the current offset which is used to calculate the pagination
        $currentPage = (int) $pos;
        
        // the number of items displayed on a page for pagination
        $resultsPerPage = (int) $num;
        if ($resultsPerPage == 0) {
            $resultsPerPage = $this->getVar('pageSize', 20);
        }
        
        $templateParameters = [
            'editorName' => $editor,
            'objectType' => $objectType,
            'sort' => $sort,
            'sortdir' => $sdir,
            'currentPage' => $currentPage,
            'onlyImages' => false,
            'imageField' => ''
        ];
        $searchTerm = '';
        
        $formOptions = [
            'object_type' => $objectType,
            'editor_name' => $editor
        ];
        $form = $this->createForm('TdM\MapsModule\Form\Type\Finder\\' . ucfirst($objectType) . 'FinderType', $templateParameters, $formOptions);
        
        if ($form->handleRequest($request)->isValid()) {
            $formData = $form->getData();
            $templateParameters = array_merge($templateParameters, $formData);
            $currentPage = $formData['currentPage'];
            $resultsPerPage = $formData['num'];
            $sort = $formData['sort'];
            $sdir = $formData['sortdir'];
            $searchTerm = $formData['q'];
            $templateParameters['onlyImages'] = isset($formData['onlyImages']) ? (bool)$formData['onlyImages'] : false;
            $templateParameters['imageField'] = isset($formData['imageField']) ? $formData['imageField'] : '';
        }
        
        $where = '';
        $orderBy = $sort . ' ' . $sdir;
        
        $qb = $repository->getListQueryBuilder($where, $orderBy);
        
        if (true === $templateParameters['onlyImages'] && $templateParameters['imageField'] != '') {
            $imageField = $templateParameters['imageField'];
            $orX = $qb->expr()->orX();
            foreach (['gif', 'jpg', 'jpeg', 'jpe', 'png', 'bmp'] as $imageExtension) {
                $orX->add($qb->expr()->like('tbl.' . $imageField, $qb->expr()->literal('%.' . $imageExtension)));
            }
        
            $qb->andWhere($orX);
        }
        
        if ($searchTerm != '') {
            $qb = $this->get('tdm_maps_module.collection_filter_helper')->addSearchFilter($objectType, $qb, $searchTerm);
        }
        $query = $repository->getQueryFromBuilder($qb);
        
        list($entities, $objectCount) = $repository->retrieveCollectionResult($query, true);
        
        $templateParameters['items'] = $entities;
        $templateParameters['finderForm'] = $form->createView();
        
        $contextArgs = ['controller' => 'external', 'action' => 'display'];
        $templateParameters = $this->get('tdm_maps_module.controller_helper')->addTemplateParameters($objectType, $templateParameters, 'controllerAction', $contextArgs);
        
        $templateParameters['pager'] = [
            'numitems' => $objectCount,
            'itemsperpage' => $resultsPerPage
        ];
        
        $output = $this->renderView('@TdMMapsModule/External/' . ucfirst($objectType) . '/find.html.twig', $templateParameters);
        
        return new PlainResponse($output);
    }
}
