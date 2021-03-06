<?php
namespace SlimApiTest\Service;

use SlimApi\Service\DependencyService;

class DependencyServiceTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;

    public function setUp()
    {
        $this->setupDirectory();
        $mvcTemplate = <<<'EOT'
<?php
$config = [];

// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------

return $config;
EOT;
        file_put_contents('config/mvc.config.php', $mvcTemplate);

        $this->dependencyService = new DependencyService('config/mvc.config.php', 'Foo');
    }

    public function testAdd()
    {
        $this->dependencyService->add('Bar', 'some template');
        $this->assertEquals($this->dependencyService->fetch('Bar'), 'some template');
    }

    public function testAddDupe()
    {
        $this->setExpectedException('InvalidArgumentException', 'Template already exists.');
        $this->dependencyService->add('Bar', 'some template');
        $this->dependencyService->add('Bar', 'some other template');
    }

    public function testFetchFalse()
    {
        $this->assertEquals($this->dependencyService->fetch('Bar'), false);
    }

    public function testTargetLocation()
    {
        $this->assertEquals($this->dependencyService->targetLocation('foo'), 'config/mvc.config.php');
    }

    public function testProcessCommand()
    {
        $this->dependencyService->add('Bar', 'some template');
        $this->dependencyService->processCommand('Bar', 'foo');
        $this->assertEquals($this->dependencyService->commands, ['some template']);
    }

    public function testProcessCommandException()
    {
        $this->setExpectedException('Exception', 'Invalid dependency command.');
        $this->dependencyService->add('Foo', 'some template');
        $this->dependencyService->processCommand('Bar', 'some template');
    }

    public function testCreate()
    {
        $this->dependencyService->add('Bar', 'some template');
        $this->dependencyService->processCommand('Bar', 'foo');
        $this->dependencyService->create('buz');

        $mvcFile = <<<'EOT'
<?php
$config = [];

// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------

some template
return $config;
EOT;

        $this->assertEquals(file_get_contents('config/mvc.config.php'), $mvcFile);
    }
}
