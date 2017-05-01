<?php

namespace Bayne\ExternalLinkBundle\Routing;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class ExternalLinkLoader extends Loader
{
    private $loaded = false;
    /**
     * @var FileLocatorInterface
     */
    private $locator;
    /**
     * @var string
     */
    private $kernelRootDirectory;

    /**
     * ExternalLinkLoader constructor.
     *
     * @param FileLocatorInterface $locator
     * @param $kernelRootDirectory
     */
    public function __construct(FileLocatorInterface $locator, $kernelRootDirectory)
    {
        $this->locator = $locator;
        $this->kernelRootDirectory = $kernelRootDirectory;
    }


    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file, $this->kernelRootDirectory.'/config');

        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        try {
            $parser = new Parser();
            $parsedConfig = $parser->parse(file_get_contents($path));
        } catch (ParseException $e) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain valid YAML.', $path), 0, $e);
        }

        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "external" loader twice');
        }

        $routes = new RouteCollection();

        foreach ($parsedConfig as $name => $values) {
            $path = '/externalURL/'.$name;
            $defaults = array(
                '_controller' => 'BayneExternalLinkBundle:ExternalLink:externalLinkRedirect',
                '_url' => $values['url']
            );

            $route = new Route(
                $path,
                $defaults,
                []
            );

            // add the new route to the route collection
            $routes->add($name, $route);
        }


        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'external' === $type;
    }

}
