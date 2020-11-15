<?php

namespace AppBundle\Helpers;

class InfoBuilder
{

	private $textElements=[];
	private $markDownElements=[];
	private $fileElements=[];
	private $buttons = [];
	private $links = [];
	private $Id;
	private $extraIds=[];

	private $path;


	public function addTextElement($name,$value)
	{
		if(empty($value))
		{
			$value = 'N/A';
		}
		
		array_push($this->textElements,[$name=>$value]);
	}

	public function addMarkDownElement($name,$value)
	{
		if(empty($value))
		{
			$value = 'N/A';
		}

		array_push($this->markDownElements,[$name=>$value]);
	}

	/**
	 * @return array
	 */
	public function getMarkDownElements()
	{
		return $this->markDownElements;
	}


	public function addFileElement($name,$value,$path)
	{
		if(!empty($value))
		{
			array_push($this->fileElements,[$name=>'/file_uploads/'.$path.'/'.$value]);
		}

	}
	
	public function getTextElements()
	{
		return $this->textElements;
	}

	public function getFileElements()
	{
		return $this->fileElements;
	}

	public function transformDate($date)
	{
		return date("d/m/Y", strtotime($date));
	}

	/**
	 * @return mixed
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param mixed $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * @return array
	 */
	public function getButtons()
	{
		return $this->buttons;
	}

	/**
	 * @param array $buttons
	 */
	public function setButtons($buttons)
	{
		 array_push($this->buttons,$buttons);
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->Id;
	}

	/**
	 * @param mixed $Id
	 */
	public function setId($Id)
	{
		$this->Id = $Id;
	}

	/**
	 * @param $name
	 * @param $path
	 * @param $icon
	 * @param $parameter
	 */
	public function setLink($name,$path,$icon,$parameter)
	{
		array_push($this->links,['name'=>$name,'icon'=>'images/icons/'.$icon.'.png','path'=>$path,'parameter'=>$parameter]);
	}
	
	public function getLinkElements()
	{
		return $this->links;
	}

	/**
	 * @return mixed
	 */
	public function getExtraIds()
	{
		return $this->extraIds;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function setExtraId($name,$value)
	{
		array_push($this->extraIds,[$name=>$value]);
	}

	

}