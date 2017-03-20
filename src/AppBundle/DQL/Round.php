<?php
namespace AppBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Round extends FunctionNode
{
    protected $roundExp;
    protected $roundPrecission;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'ROUND(' .
        $sqlWalker->walkArithmeticExpression($this->roundExp) . ','.
        $sqlWalker->walkArithmeticExpression($this->roundPrecission)
        .')';
    }

    /**
     * parse - allows DQL to breakdown the DQL string into a processable structure
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->roundExp = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->roundPrecission = $parser->ArithmeticExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}