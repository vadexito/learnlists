<?php 

namespace Question\View\Helper;

use Zend\View\Helper\AbstractHelper;
use DataGrid\DataGrid;
use DataGrid\Renderer\HtmlTable;

class Grid extends AbstractHelper
{
    protected $count = 0;

    public function __invoke($lists)
    {
        $data = [];
        foreach ($lists as $list){
            $data[] = [
                'id' => $list->id,
                'title' => $list->title,
                'author' => $list->author,
                'date' => $list->creationDate->format(\DateTime::ISO8601),
                'questions' => count($list->questions)
            ];
        }
        
        $grid = new DataGrid($data);
        $grid->setSpecialColumn('options', function($row){
            $listId = $row['id'];
            
            $btnEdit = '<a href="'
                .$this->getView()->url('list/show',['id' => $listId])
                .'" class="btn"><i class="icon-edit"></i></a>';
            
            $btnDelete = '<a href="'
                .$this->getView()->url('list/delete',['id' => $listId])
                .'" class="btn"><i class="icon-trash"></i></a>';
            
            
            $content = $btnEdit.$btnDelete;
            return '<div class="btn-toolbar"><div class="btn-group">'
                .$content
                .'<\div><\div>';
        });
        
        $grid->setRenderer(new HtmlTable());
        
        return $grid->render();
    }
}