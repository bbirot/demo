<?php
/**
 * Demo annuaire
 * 1) gestion d'une arbo categories multi-niveau
 * 2) gestion de fiches annuaire liees a une ou +ieurs categories
 *
 * Gestion mode liste : consultation des fiches
 *
 * @title       List_Controller
 * @author      Benoit BIROT
 * @date        Janvier 2013
 *
 **/

class List_Controller
{
    private $templateTitle = 'Annuaire \ Vue liste';
    private $page = 'list';

    /**
     * Fonction principale lancee par router.php
     *
     * @param array $getVars les variables GET
     */
    public function main(array $getVars)
    {
        // vue par défaut
        $action = isset($getVars['action'])? $getVars['action']: 'list';
        $idCategory = isset($getVars['id'])? $getVars['id']: 1;

        // routage selon l'action demandee
        switch($action){
            default:
                $this->listAction($idCategory);
        }
    }

    /**
     * Vue liste des fiches selon une categorie et sa descendance
     *
     * @param int $idCategory id categorie
     */
    public function listAction($idCategory)
    {
        $categoryModel = new Category_Model();
        $recordModel = new Record_Model();
        $treeViewModel = new TreeView_Model();

        // menu categories
        $listCategory = $categoryModel->get_listCategory();
        $htmlMenu = $treeViewModel->get_htmlMenu($listCategory,"list",$idCategory);

        // categories enfants de la categorie select
        $childrenCategory = $categoryModel->get_listCategory($idCategory);

        // fiches categories select + enfants
        $records = $recordModel->get_recordsCategory($idCategory);
        // + fiches categories enfants
        foreach ($childrenCategory as $value){
            $records = array_merge($records,$recordModel->get_recordChildrenCategory($value));
        }

        // recup fil ariane categorie pour chaque fiche
        foreach ($records as $key=>$value){
            $records[$key]->set_breadcrumb($categoryModel->get_categoryBreadcrumb($records[$key]->get_categories()));
        }

        // prepa template
        $header = new View_Model('header');
        $footer = new View_Model('footer');
        $view = new View_Model('list');

        // variables transmises au template
        $header->assign('page' , $this->page);
        $view->assign('header', $header->render(FALSE));
        $view->assign('footer', $footer->render(FALSE));
        $view->assign('templateTitle' , $this->templateTitle);
        $view->assign('listCategory' , $listCategory);
        $view->assign('records' , $records);
        $view->assign('htmlMenu' , $htmlMenu);

        // vue
        $view->render();
    }
}