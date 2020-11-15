<?php


namespace AppBundle\Helpers;


use AppBundle\Security\ACLSecurityProvider;
use Symfony\Component\HttpFoundation\RequestStack;

class GridBuilder
{

    private $gridHeaders = [];
    private $startIndex;
    private $buttons;
    private $path;
    private $secondaryPath;
    private $parentValue;
    private $approveStatusValues = [];
    private $declineStatusValues = [];
    private $currentObject;
    private $ignoredButtons;
    

    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var ACLSecurityProvider
     */
    private $acl;


    public function __construct(RequestStack $requestStack,ACLSecurityProvider $acl)
    {
        $this->requestStack = $requestStack;
        $this->acl = $acl;
        $this->approveStatusValues = [false];
        $this->declineStatusValues = [true];
    }
    
    /**
     * @return array
     */
    public function getGridHeaders()
    {
        return $this->gridHeaders;
    }
    
    public function addGridHeader($name,$path,$columnType,$isFilter=false)
    {
        $nextSort = 'asc';
        
        $request = $this->requestStack->getCurrentRequest();
        
        if($request->get('sortBy') === $path && $path!=null)
        {
            if($request->get('sortType') === 'asc')
            {
                $currentSort = 'asc';
                $nextSort = 'desc';
            }
            else
            {
                $currentSort = 'desc';
            }
        }
        else
        {
            $currentSort='both';
        }

        $filterName = $path;
        
        $class = 'sort-'.$currentSort;

        if($columnType == 'index')
        {
            $class.=' number';
        }

        $searchBy = $request->get('searchBy');
        $searchText = $request->get('searchText');
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');


        if($searchBy!=null && ($searchText!=null || $fromDate!=null && $toDate!=null))
        {
            $path = implode ('&',array($path,
                'searchBy='.$searchBy,
                'searchText='.$searchText,
                'fromDate='.$fromDate,
                'toDate='.$toDate
            ));
        }

        array_push($this->gridHeaders,array(
            'name'=>$name,
            'link'=>$path,
            'class'=>$class,
            'sortType'=>$nextSort,
            'columnType'=>$columnType,
            'filterName'=>$filterName,
            'isFilter'=>$isFilter
        ));
    }

    public function setStartIndex($page,$perPageLimit)
    {
        $this->startIndex = 1+($page-1)*$perPageLimit;
    }

    public function getStartIndex()
    {
        return $this->startIndex;
    }

    public function setButtons()
    {
        $this->buttons = $this->acl->getCurrentACLs($this->currentObject);
    }

    public function getButtons()
    {
        return $this->buttons;
    }


    public function setPath($path)
    {
        $this->path = $path;
    }


    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getSecondaryPath()
    {
        return $this->secondaryPath;
    }

    /**
     * @param mixed $secondaryPath
     */
    public function setSecondaryPath($secondaryPath)
    {
        $this->secondaryPath = $secondaryPath;
    }

    /**
     * @return mixed
     */
    public function getParentValue()
    {
        return $this->parentValue;
    }

    /**
     * @param $parentValue
     */
    public function setParentValue($parentValue)
    {
        $this->parentValue = $parentValue;
    }

    /**
     * @return array
     */
    public function getApproveStatusValues()
    {
        return $this->approveStatusValues;
    }

    /**
     * @param array $approveStatusValues
     */
    public function setApproveStatusValues($approveStatusValues)
    {
        $this->approveStatusValues = $approveStatusValues;
    }

    /**
     * @return array
     */
    public function getDeclineStatusValues()
    {
        return $this->declineStatusValues;
    }

    /**
     * @param array $declineStatusValues
     */
    public function setDeclineStatusValues($declineStatusValues)
    {
        $this->declineStatusValues = $declineStatusValues;
    }

    /**
     * @return mixed
     */
    public function getCurrentObject()
    {
        return $this->currentObject;
    }

    /**
     * @param mixed $currentObject
     */
    public function setCurrentObject($currentObject)
    {
        $this->currentObject = $currentObject;
    }

    /**
     * @return mixed
     */
    public function getIgnoredButtons()
    {
        return $this->ignoredButtons;
    }

    /**
     * @param mixed $ignoredButtons
     */
    public function setIgnoredButtons($ignoredButtons)
    {
        $this->ignoredButtons = $ignoredButtons;
    }


}