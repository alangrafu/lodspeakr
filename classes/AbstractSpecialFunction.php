<?

abstract class AbstractSpecialFunction{
  abstract protected function getFunction($uri);
  abstract protected function getParams($uri);
  abstract public function execute($uri, $context);

}

?>
