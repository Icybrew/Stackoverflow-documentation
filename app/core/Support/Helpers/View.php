<?php


namespace App\Core\Support\Helpers;


use App\Core\Config;
use App\Core\Data;
use App\Core\Router;
use App\Core\Support\Interfaces\Renderable;
use Twig\Markup;

/**
 * Class View
 * @package App\Core\Support\Helpers
 */
class View implements Renderable
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var array
     */

    private $_data;

    /**
     * View constructor.
     * @param string $name
     * @param array $data
     */
    public function __construct(string $name, array $data = [])
    {
        $this->_name = $name;
        $this->_data = array_merge($data, Data::get('viewData') ?? []);
    }

    /**
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(): void
    {
        $name = $this->_name . '.php.twig';
        $viewPath = '../resources/views/' . $name;

        $twigLoader = new \Twig\Loader\FilesystemLoader('../resources/views');
        $twig = new \Twig\Environment($twigLoader);

        // Asset function
        $twig->addFunction(new \Twig\TwigFunction('asset', function (?string $asset): ?string {
            return sprintf(Config::get('config', 'root') . '%s', rtrim($asset, '/'));
        }));

        // Method function
        $twig->addFunction(new \Twig\TwigFunction('method', function (?string $method): ?Markup {
            $field = '<input type="hidden" name="_method" value="' . $method . '">';
            return new \Twig\Markup($field, 'UTF-8');
        }));

        // Route function
        $twig->addFunction(new \Twig\TwigFunction('route', function (?string $name, array $param = []): ?string {
            $route = Router::findRouteByName($name);
            if (is_null($route)) return null;

            try {
                $url = $route->getUrl($param);
            } catch (\Throwable $ex) {
                return $ex;
            }

            return sprintf(Config::get('config', 'root') . '%s', $url);
        }));

        if (file_exists($viewPath)) {
            $twig->display($name, $this->_data);
        } else {
            throw new \Error("View '$viewPath' not found");
        }
    }
}