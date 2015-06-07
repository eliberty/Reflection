<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Method as MethodDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Strategy to create MethodDescriptor and arguments when applicable.
 */
final class Method implements ProjectFactoryStrategy
{

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof ClassMethod;
    }

    /**
     * Creates an MethodDescriptor out of the given object including its child elements.
     *
     * @param object $object object to convert to an MethodDescriptor
     * @param StrategyContainer $strategies used to convert nested objects.
     *
     * @return MethodDescriptor
     *
     * @throws InvalidArgumentException when this strategy is not able to handle $object
     */
    public function create($object, StrategyContainer $strategies)
    {
        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf('%s cannot handle objects with the type %s',
                    __CLASS__,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        $method = new MethodDescriptor(new Fqsen($object->name));

        foreach ($object->params as $param) {
            $strategy = $strategies->findMatching($param);
            $method->addArgument($strategy->create($param, $strategies));
        }

        return $method;
    }
}