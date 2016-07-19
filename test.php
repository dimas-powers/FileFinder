<?php

 
  interface FileFinder {

/**
     * Find only files
     * @return FileFinder
     */
    public function isFile();


/**
     * Find only directories
     * @return FileFinder
     */
    public function isDir();


/**
     * Search in directory $dir
     * @param string $dir
     * @return FileFinder
     */
   //public function inDir($dir);


/**
     * Filter by regular expression on path
     * @param string $regularExpression
     * @return FileFinder
     */
    //public function match($regularExpression);


/**
     * Returns array of all found files/dirs (full path)
     * @return string[]
     */
    public function getList();

  }


class FileFinderImplementation implements FileFinder {

  protected $is_file;
  protected $is_dir;
  protected $in_dir;
  protected $reg_expr;

  function __construct ( $is_file, $is_Dir, $in_dir, $reg_expr) {
    $this->is_file = $is_file;
    $this->is_dir = $is_dir;
    $this->in_dir = $in_dir;
    $this->reg_expr = $reg_expr;
  }

	public function isFile () {
		$this->is_file = true;	
    return $this;
	}
	
	public function isDir () {
    $this->is_dir = true;
    return $this;
	}
	
	public function inDir ( $in_dir ) {
		$this->in_dir[] = $in_dir;
		return $this;
	}
	
	public function match ( $reg_expr ) {
    $this->reg_expr[] = $reg_expr;
  return $this;  
	}

  public function getList () {
  if(empty($this->in_dir)){
    throw new Exception('There is no DIR');
  }
    foreach ($this->in_dir as $dir){
      $all_files = glob($dir.'*.*');
        foreach($all_files as $file){
            if($this->reg_expr){
              foreach($this->reg_expr as $match){
                if(preg_match($match, $file)) {
                  $result[] = $file;
                }  
              }
            }
            else{
              $result[] = $file;
            }
        }
    }
  return $result;
  }

}


# search for all .conf or .ini files in directories /etc/ and /var/log/

$fileList = new FileFinderImplementation();

$fileList
    ->isFile()
    ->inDir('etc/')
    ->inDir('var/log/')
    ->match('/.*\.conf$/')
    ->match('/.*\.ini$/');
$files = $fileList->getList();
foreach ($files as $file) {
  if( $file != '.' && $file != '..' ){
    print ($file . "</br>");
  }
}


  #  search for all files in /tmp
  $fileList = (new FileFinderImplementation());
  $fileList
    ->isFile()
    ->inDir('tmp/');
  $files = $fileList->getList();
  foreach ($files as $file) {
print $file . "</br>";
  }

# should throw an exception if no dirs were provided
  $files = (new FileFinderImplementation());
  $files
    ->isFile()
    ->match('/.*\.ini$/')
    ->getList(); # -> exception


