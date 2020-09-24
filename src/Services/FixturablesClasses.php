<?php


namespace Steven\AutoFixturesBundle\Services;

use App\Kernel;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

/**
 * get a list of all fixturables classes
 * Class FixturablesClasses
 * @package Steven\AutoFixturesBundle\Services
 */
class FixturablesClasses
{

    /**
     * @var Finder
     */
    private $finder;
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(Finder $finder, ParameterBagInterface $params)
    {
        $this->finder = $finder;
        $this->params = $params;
    }

    /**
     * return all files path inside the Entity
     * directory
     * @return array
     */
    public function getClassNames()
    {
        // get all files in Entity folder
        $this->finder->files()->name('*.php')->in($this->params->get('kernel.project_dir') . '/src/Entity');
        $classes = [];
        if ($this->finder->hasResults()) {
            foreach ($this->finder as $file) {
                $classes[] = $this->getClassNamespaceFromFile($file->getPathname())."\\".$file->getFilenameWithoutExtension();
            }
        }
        return $classes;
    }

    /**
     * get the class namespace form file path using token
     *
     * @param $filePathName
     *
     * @return  null|string
     */
    public function getClassNamespaceFromFile($filePathName)
    {
        $src = file_get_contents($filePathName);

        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        if (!$namespace_ok) {
            return null;
        } else {
            return $namespace;
        }
    }

}
