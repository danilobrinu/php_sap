--TEST--
Sap::fetchFunction() basic behavior
--SKIPIF--
<?php
$config = include 'config.inc';

if (!$config) {
	print 'skip connection configuration not available';
}
?>
--FILE--
<?php
class InvalidSapFunction {}

class CustomSapFunction extends SapFunction {}

class OtherCustomSapFunction extends SapFunction
{
	private $arg;
	
	public function __construct($arg)
	{
		$this->arg = $arg;
	}
	
	public function getArg()
	{
		return $this->arg;
	}
}

class RfcPing extends OtherCustomSapFunction
{
	public function getName(): string
	{
		return 'RFC_PING';
	}
}

$config = include 'config.inc';

$s = new Sap($config);

/** test fetchFunction returns SapFunction object when fetching remote function */
$f = $s->fetchFunction('RFC_PING');
var_dump(get_class($f));
var_dump($f->getName());

/** test fetchFunction returns custom SapFunction object when a custom function class has been set */
$s->setFunctionClass(CustomSapFunction::class);
$f = $s->fetchFunction('RFC_PING');
var_dump(get_class($f));

/** test fetchFunction returns requested type of SapFunction if 2nd argument provided */
$f = $s->fetchFunction('RFC_PING', SapFunction::class);
var_dump(get_class($f));

/** test fetchFunction properly handles constructor of custom function class */
$f = $s->fetchFunction('RFC_PING', RfcPing::class, ['test']);
var_dump(get_class($f));
var_dump($f->getArg());

/** test fetchFunction accepts SapFunction object as 1st argument and returns the same object */
$func = new RfcPing('arg');
$f = $s->fetchFunction($func);
var_dump($f === $func);

/** test fetchFunction returns callable object */
var_dump(gettype($f()));
?>
--EXPECT--
string(11) "SapFunction"
string(8) "RFC_PING"
string(17) "CustomSapFunction"
string(11) "SapFunction"
string(7) "RfcPing"
string(4) "test"
bool(true)
string(5) "array"