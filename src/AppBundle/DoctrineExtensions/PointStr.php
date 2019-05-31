<?php
/**
 *  Class for the database POINTSTR function to work
 *  @category Extension
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */

namespace AppBundle\DoctrineExtensions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * POINT_STR function for querying using Point objects as parameters
 *
 * Usage: POINT_STR(:param) where param should be mapped to $point where $point is of point class type
 *        without any special typing provided (eg. so that it gets converted to string)
 * Class PointStr
 * @package AppBundle\DoctrineExtensions
 */
class PointStr extends FunctionNode
{
    private $arg;

    /**
     * sql syntax for the function
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'GeomFromText(' . $this->arg->dispatch($sqlWalker) . ')';
    }

    /**
     * Parsing the sql syntax as used in the doctrine query
     * @param \Doctrine\ORM\Query\Parser $parser
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->arg = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
