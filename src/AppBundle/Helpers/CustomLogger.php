<?php

namespace AppBundle\Helpers;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;

class CustomLogger
{

	private $path;
	private $displayLog;
	public $output;
	public $fileLogName;

	
	public function __construct(ConsoleOutput $output)
	{
		$this->output = $output;
	}

	/**
	 * @return mixed
	 */
	public function getDisplayLog()
	{
		return $this->displayLog;
	}

	/**
	 * @param mixed $displayLog
	 */
	public function setDisplayLog($displayLog)
	{
		$this->displayLog = $displayLog;
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
	 * @return mixed
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @param mixed $output
	 */
	public function setOutput(OutputInterface $output)
	{
		$this->output = $output;
	}

	/**
	 * @param $content
	 */
	public function writeLog($content)
	{
		if($this->displayLog)
			$this->output->writeln($content);
		try
		{
			file_put_contents($this->path, $content . "\n", FILE_APPEND | LOCK_EX);
		}
		catch (ContextErrorException $e)
		{

			$files = explode('/',$this->path);
			array_pop($files);
			$dir = implode('/',$files);
			mkdir($dir);
			file_put_contents($this->path, $content . "\n", FILE_APPEND | LOCK_EX);
		}

	}

	public function createLogPath($filePath)
	{
		$fileLogPath = explode("/",$filePath);

		$fileLogName = end($fileLogPath);

		$fileLogName = str_replace('csv','txt',$fileLogName);

		$key = key($fileLogPath);

		$fileLogPath[$key]=$fileLogName;
		
		$this->fileLogName = $fileLogName;

		$fileLogPath =implode('/',$fileLogPath);
		
		return $fileLogPath;
	}

	/**
	 * @return mixed
	 */
	public function getFileLogName()
	{
		return $this->fileLogName;
	}

	/**
	 * @param mixed $fileLogName
	 */
	public function setFileLogName($fileLogName)
	{
		$this->fileLogName = $fileLogName;
	}

	public function setLogFileDetails($filePath)
	{
		$fileLogPath = explode("/",$filePath);

		$fileLogName = end($fileLogPath);

		$fileLogName = 'log_'.$fileLogName;

		$fileLogNamePath = '/log/'.$fileLogName;

		$key = key($fileLogPath);

		$fileLogPath[$key]=$fileLogNamePath;

		$this->path = implode('/',$fileLogPath);

		$this->fileLogName = $fileLogName;

	}

}