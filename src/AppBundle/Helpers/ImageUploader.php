<?php

namespace AppBundle\Helpers;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;

class ImageUploader
{
	/**
	 * @var RequestStack
	 */
	private $requestStack;

	private $uploadPath;
	
	/**
	 * CroppicUploader constructor.
	 * @param RequestStack $requestStack
	 */
	public function __construct(RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
	}

	/**
	 * @return mixed
	 */
	public function getUploadPath()
	{
		return $this->uploadPath;
	}

	/**
	 * @param mixed $uploadPath
	 */
	public function setUploadPath($uploadPath)
	{
		$this->uploadPath = $uploadPath;
	}

	public function deleteImage($fileName){

		$filePath = $this->uploadPath.'/'.$fileName;

		$fs = new Filesystem();

		if($fs->exists($filePath) && !empty($fileName))
		{
			unlink($filePath);
			//$fs->remove(array('symlink',$this->absolutePath, $fileName));
		}

		return true;
	}
	
	public function uploadImage(){

		$request = $this->requestStack->getCurrentRequest();

		$imageData = $request->get('image');
		$primaryIdentifier = $request->get('primaryIdentifier');

		//here you can detect if type is png or jpg if you want
		$imageName = 'img_'.md5(uniqid()).'.jpg';
		$filePath=$this->getUploadPath().'/'.$imageName;

		$imageData = explode(',', $imageData);

		$allowedTypes = array('data:image/png;base64','');
		
		if(!in_array($imageData[0],$allowedTypes))
		{
			return ['status'=>'failed','message'=>'File type not supported'];
		}

		$imageData = $imageData [1];

		$imageData = base64_decode($imageData);

		// Create Image From Existing File
		$img = imagecreatefromstring($imageData);


		if(!file_exists($this->getUploadPath()))
			mkdir($this->getUploadPath());

		// Send Image to Path
		imagejpeg($img,$filePath);

		// Clear Memory
		imagedestroy($img);

		return [
			'status'=>'success',
			'message'=>'File successfully uploaded',
			'primaryIdentifier'=>$primaryIdentifier,
			'fileName'=>$imageName
		];
	}
	

}