<?php

namespace Drupal\pw_regiostars\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default argument plugin to extract a regiostars project category
 *
 * @ViewsArgumentDefault(
 *   id = "regiostars_project_category",
 *   title = @Translation("Regiostars project category ID of the current node")
 * )
 */
class RegiostarsProjectCategory extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

	/**
	 * The route match.
	 *
	 * @var \Drupal\Core\Routing\RouteMatchInterface
	 */
	protected $routeMatch;

	/**
	 * Constructs a new Node instance.
	 *
	 * @param array $configuration
	 *   A configuration array containing information about the plugin instance.
	 * @param string $plugin_id
	 *   The plugin_id for the plugin instance.
	 * @param mixed $plugin_definition
	 *   The plugin implementation definition.
	 * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
	 *   The route match.
	 */
	public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
		parent::__construct($configuration, $plugin_id, $plugin_definition);

		$this->routeMatch = $route_match;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
		return new static(
				$configuration,
				$plugin_id,
				$plugin_definition,
				$container->get('current_route_match')
				);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getArgument() {
		if (($node = $this->routeMatch->getParameter('node')) && $node instanceof NodeInterface) {
			return $node->get('field_regiostars_category')->target_id;;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCacheMaxAge() {
		return Cache::PERMANENT;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCacheContexts() {
		return ['url'];
	}

}
