<?php
namespace TYPO3\TYPO3CR\Migration\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3CR".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\QueryInterface;
use TYPO3\Flow\Persistence\Repository;

/**
 * Repository for MigrationStatus instances.
 *
 * @Flow\Scope("singleton")
 */
class MigrationStatusRepository extends Repository {

	/**
	 * @var array
	 */
	protected $defaultOrderings = array(
		'version' => QueryInterface::ORDER_ASCENDING
	);

}
