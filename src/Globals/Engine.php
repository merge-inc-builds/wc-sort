<?php
declare(strict_types=1);

namespace MergeInc\WcSort\Globals;

final class Engine {

	/**
	 * @var string
	 */
	private string $templatesDir;

	/**
	 * @var array
	 */
	private array $cache = array();

	/**
	 * @param string $templatesDir
	 */
	public function __construct( string $templatesDir ) {
		$this->templatesDir = $templatesDir;
	}

	/**
	 * @param string $template
	 * @param array  $data
	 * @param bool   $html
	 *
	 * @return string
	 */
	public function render( string $template, array $data, bool $html ): string {
		$cache = true;
		if ( ! $fn = $this->cache[ $cacheKey = "$this->templatesDir/$template" ] ?? false ) {
			$cache                    = false;
			$this->cache[ $cacheKey ] = $fn = require "$cacheKey.php";
		}

		return $this->minify( $fn( $data ) . ( ( $html && $cache ) ? '<!-- Cached Content -->' : '' ) );
	}

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	private function minify( string $html ): string {
		return preg_replace( '/\s+/', ' ', $html );
	}
}
