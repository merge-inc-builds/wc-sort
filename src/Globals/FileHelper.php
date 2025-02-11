<?php
declare(strict_types=1);

namespace MergeInc\WcSort\Globals;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

final class FileHelper {

	/**
	 * @param string $dir
	 * @param string $extension
	 * @return array
	 */
	public function getAllFilesRecursively( string $dir, string $extension = '*' ): array {
		$files    = array();
		$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );

		foreach ( $iterator as $file ) {
			if ( $file->isFile() && ( $extension === '*' || $file->getExtension() === $extension ) ) {
				$files[] = $file->getPathname();
			}
		}

		usort(
			$files,
			function ( $a, $b ) {
				return strcmp( $a, $b );
			}
		);

		return $files;
	}
}
