<?php
/**
 *  Class for the database STDistanceSphere function to work
 *  @category Extension
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\DoctrineExtensions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * DQL function for calculating distances between two points
 *
 * Example: DISTANCE(foo.point, POINT_STR(:param))
 * Class STDistanceSphere
 * @package AppBundle\DoctrineExtensions
 */
class STDistanceSphere extends FunctionNode
{

    private $firstArg;
    private $secondArg;

    /**
     * sql syntax for the function
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        //Need to do this hacky linestring length thing because
        //despite what MySQL manual claims, DISTANCE isn't actually implemented...
        return 'ST_Distance_Sphere(' .
            $this->firstArg->dispatch($sqlWalker) .
            ', ' .
            $this->secondArg->dispatch($sqlWalker) .
            ')';
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
        $this->firstArg = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondArg = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
