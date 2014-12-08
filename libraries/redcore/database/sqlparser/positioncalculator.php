<?php
/**
 * @package     Redcore
 * @subpackage  Exception
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_REDCORE') or die;

/**
 * This file implements the calculator for the position elements of
 * the output of the RDatabaseSqlparserSqlparser.
 *
 * Copyright (c) 2010-2012, Justin Swanhart
 * with contributions by André Rothe <arothe@phosco.info, phosco@gmx.de>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 */

/**
 * 
 * This class calculates the positions 
 * of base_expr within the original SQL statement.
 * 
 * @author arothe
 * 
 */
class RDatabaseSqlparserPositioncalculator extends RDatabaseSqlparserSqlparserutils {

    private static $allowedOnOperator = array("\t", "\n", "\r", " ", ",", "(", ")", "_", "'", "\"");
    private static $allowedOnOther = array("\t", "\n", "\r", " ", ",", "(", ")", "<", ">", "*", "+", "-", "/", "|",
                                           "&", "=", "!", ";");

    private function printPos($text, $sql, $charPos, $key, $parsed, $backtracking) {
        if (!isset($_ENV['DEBUG'])) {
            return;
        }

        $spaces = "";
        $caller = debug_backtrace();
        $i = 1;
        while ($caller[$i]['function'] === 'lookForBaseExpression') {
            $spaces .= "   ";
            $i++;
        }
        $holdem = substr($sql, 0, $charPos) . "^" . substr($sql, $charPos);
        echo $spaces . $text . " key:" . $key . "  parsed:" . $parsed . " back:" . serialize($backtracking) . " "
                . $holdem . "\n";
    }

    public function setPositionsWithinSQL($sql, $parsed) {
        $charPos = 0;
        $backtracking = array();
        $this->lookForBaseExpression($sql, $charPos, $parsed, 0, $backtracking);
        return $parsed;
    }

    private function findPositionWithinString($sql, $value, $expr_type) {

        $offset = 0;
        $ok = false;
        while (true) {

            $pos = strpos($sql, $value, $offset);
            if ($pos === false) {
                break;
            }

            $before = "";
            if ($pos > 0) {
                $before = $sql[$pos - 1];
            }

            $after = "";
            if (isset($sql[$pos + strlen($value)])) {
                $after = $sql[$pos + strlen($value)];
            }

	        // if we have an operator, it should be surrounded by
	        // whitespace, comma, parenthesis, digit or letter, end_of_string
	        // an operator should not be surrounded by another operator

            if ($expr_type === 'operator') {

                $ok = ($before === "" || in_array($before, self::$allowedOnOperator, true))
                        || (strtolower($before) >= 'a' && strtolower($before) <= 'z')
                        || ($before >= '0' && $before <= '9');
                $ok = $ok
                        && ($after === "" || in_array($after, self::$allowedOnOperator, true)
                                || (strtolower($after) >= 'a' && strtolower($after) <= 'z')
                                || ($after >= '0' && $after <= '9') || ($after === '?') || ($after === '@'));

                if (!$ok) {
                    $offset = $pos + 1;
                    continue;
                }

                break;
            }

	        // in all other cases we accept
	        // whitespace, comma, operators, parenthesis and end_of_string

            $ok = ($before === "" || in_array($before, self::$allowedOnOther, true));
            $ok = $ok && ($after === "" || in_array($after, self::$allowedOnOther, true));

            if ($ok) {
                break;
            }

            $offset = $pos + 1;
        }

        return $pos;
    }

    private function lookForBaseExpression($sql, &$charPos, &$parsed, $key, &$backtracking) {
        if (!is_numeric($key)) {
            if (($key === 'UNION' || $key === 'UNION ALL') || ($key === 'expr_type' && $parsed === 'expression')
                    || ($key === 'expr_type' && $parsed === 'subquery')
                    || ($key === 'expr_type' && $parsed === 'bracket_expression')
                    || ($key === 'expr_type' && $parsed === 'table_expression')
                    || ($key === 'expr_type' && $parsed === 'record')
                    || ($key === 'expr_type' && $parsed === 'in-list') || ($key === 'alias' && $parsed !== false)) {
	            // we hold the current position and come back after the next base_expr
	            // we do this, because the next base_expr contains the complete expression/subquery/record
	            // and we have to look into it too
                $backtracking[] = $charPos;

            } elseif (($key === 'ref_clause' || $key === 'columns') && $parsed !== false) {
	            // we hold the current position and come back after n base_expr(s)
	            // there is an array of sub-elements before (!) the base_expr clause of the current element
	            // so we go through the sub-elements and must come at the end
                $backtracking[] = $charPos;
                for ($i = 1; $i < count($parsed); $i++) {
                    $backtracking[] = false; // backtracking only after n base_expr!
                }
            } elseif ($key === 'sub_tree' && $parsed !== false) {
	            // we prevent wrong backtracking on subtrees (too much array_pop())
	            // there is an array of sub-elements after(!) the base_expr clause of the current element
	            // so we go through the sub-elements and must not come back at the end
                for ($i = 1; $i < count($parsed); $i++) {
                    $backtracking[] = false;
                }
            } else {
	            // move the current pos after the keyword
	            // SELECT, WHERE, INSERT etc.
                if (in_array($key, parent::$reserved)) {
                    $charPos = stripos($sql, $key, $charPos);
                    $charPos += strlen($key);
                }
            }
        }

        if (!is_array($parsed)) {
            return;
        }

        foreach ($parsed as $key => $value) {
            if ($key === 'base_expr') {

	            // $this->printPos("0", $sql, $charPos, $key, $value, $backtracking);

                $subject = substr($sql, $charPos);
                $pos = $this->findPositionWithinString($subject, $value,
                        isset($parsed['expr_type']) ? $parsed['expr_type'] : 'alias');
                if ($pos === false) {
                    throw new Exception("cannot calculate position of " . $value . " within " . $subject, 5);

                }

                $parsed['position'] = $charPos + $pos;
                $charPos += $pos + strlen($value);

	            // $this->printPos("1", $sql, $charPos, $key, $value, $backtracking);

                $oldPos = array_pop($backtracking);
                if (isset($oldPos) && $oldPos !== false) {
                    $charPos = $oldPos;
                }

	            // $this->printPos("2", $sql, $charPos, $key, $value, $backtracking);

            } else {
                $this->lookForBaseExpression($sql, $charPos, $parsed[$key], $key, $backtracking);
            }
        }
    }
}
