<?php
/**
 * @package     Redcore
 * @subpackage  Exception
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_REDCORE') or die;

/**
 * A pure PHP SQL creator, which generates SQL from the output of RDatabaseSqlparserSqlparser.
 * 
 * Copyright (c) 2012, André Rothe <arothe@phosco.info, phosco@gmx.de>
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

class RDatabaseSqlparserSqlcreator {

	public $created;

	public function __construct($parsed = null) {
		if ($parsed) {
			$this->create($parsed);
		}
	}

	public function create($parsed) {
		$k = key($parsed);
		switch ($k) {

		case "UNION":
		case "UNION ALL":
			throw new Exception($k . " not implemented.", 20);
			break;
		case "SELECT":
			$this->created = $this->processSelectStatement($parsed);
			break;
		case "INSERT":
			$this->created = $this->processInsertStatement($parsed);
			break;
		case "DELETE":
			$this->created = $this->processDeleteStatement($parsed);
			break;
		case "UPDATE":
			$this->created = $this->processUpdateStatement($parsed);
			break;
		default:
			throw new Exception($k . " not implemented.", 20);
			break;
		}
		return $this->created;
	}

	protected function processSelectStatement($parsed) {
		$sql = $this->processSELECT($parsed['SELECT']) . " " . $this->processFROM($parsed['FROM']);
		if (isset($parsed['WHERE'])) {
			$sql .= " " . $this->processWHERE($parsed['WHERE']);
		}
		if (isset($parsed['GROUP'])) {
			$sql .= " " . $this->processGROUP($parsed['GROUP']);
		}
		if (isset($parsed['ORDER'])) {
			$sql .= " " . $this->processORDER($parsed['ORDER']);
		}
		if (isset($parsed['LIMIT'])) {
			$sql .= " " . $this->processLIMIT($parsed['LIMIT']);
		}
		return $sql;
	}

	protected function processInsertStatement($parsed) {
		return $this->processINSERT($parsed['INSERT']) . " " . $this->processVALUES($parsed['VALUES']);
		// TODO: subquery?
	}

	protected function processDeleteStatement($parsed) {
		return $this->processDELETE($parsed['DELETE']) . " " . $this->processFROM($parsed['FROM']) . " "
				. $this->processWHERE($parsed['WHERE']);
	}

	protected function processUpdateStatement($parsed) {
		$sql = $this->processUPDATE($parsed['UPDATE']) . " " . $this->processSET($parsed['SET']);
		if (isset($parsed['WHERE'])) {
			$sql .= " " . $this->processWHERE($parsed['WHERE']);
		}
		return $sql;
	}

	protected function processDELETE($parsed) {
		$sql = "DELETE";
		foreach ($parsed['TABLES'] as $v) {
			$sql .= $v . ",";
		}
		return substr($sql, 0, -1);
	}

	protected function processSELECT($parsed) {
		$sql = "";

		foreach ($parsed as $k => $v) {
			$len = strlen($sql);
			$sql .= $this->processColRef($v);
			$sql .= $this->processSelectExpression($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processSelectBracketExpression($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('SELECT', $k, $v, 'expr_type');
			}

			$sql .= ",";
		}
		$sql = substr($sql, 0, -1);
		return "SELECT " . $sql;
	}

	protected function processFROM($parsed) {
		$sql = "";
		foreach ($parsed as $k => $v) {
			$len = strlen($sql);
			$sql .= $this->processTable($v, $k);
			$sql .= $this->processTableExpression($v, $k);
			$sql .= $this->processSubquery($v, $k);
			$sql .= $this->processConstant($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('FROM', $k, $v, 'expr_type');
			}

			$sql .= " ";
		}
		return "FROM " . substr($sql, 0, -1);
	}

	protected function processORDER($parsed) {
		$sql = "";
		foreach ($parsed as $k => $v) {
			$len = strlen($sql);
			$sql .= $this->processOrderByAlias($v);
			$sql .= $this->processColRef($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processOrderByExpression($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('ORDER', $k, $v, 'expr_type');
			}

			$sql .= ",";
		}
		$sql = substr($sql, 0, -1);
		return "ORDER BY " . $sql;
	}

	protected function processLIMIT($parsed) {
		$sql = (!empty($parsed['offset']) ? $parsed['offset'] . ", " : "");
		$sql .= (!empty($parsed['rowcount']) ? $parsed['rowcount'] : "");
		if ($sql === "") {
			throw new RDatabaseSqlparserExceptioncreatesql('LIMIT', 'rowcount', $parsed, 'rowcount');
		}
		return "LIMIT " . $sql;
	}

	protected function processGROUP($parsed) {
		$sql = "";
		foreach ($parsed as $k => $v) {
			$len = strlen($sql);
			$sql .= $this->processColRef($v);
			$sql .= $this->processPosition($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processGroupByAlias($v);
			$sql .= $this->processGroupByExpression($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('GROUP', $k, $v, 'expr_type');
			}

			$sql .= ",";
		}
		$sql = substr($sql, 0, -1);
		return "GROUP BY " . $sql;
	}

	protected function processGroupByAlias($parsed) {
		if ($parsed['expr_type'] !== 'alias') {
			return "";
		}
		return $parsed['base_expr'];
	}

	protected function processPosition($parsed) {
		if ($parsed['expr_type'] !== 'pos') {
			return "";
		}
		return $parsed['base_expr'];
	}

	protected function processRecord($parsed) {
		if ($parsed['expr_type'] !== 'record') {
			return "";
		}
		$sql = "";
		foreach ($parsed['data'] as $k => $v) {
			$len = strlen($sql);
			$sql .= $this->processConstant($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processOperator($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('record', $k, $v, 'expr_type');
			}

			$sql .= ",";
		}
		$sql = substr($sql, 0, -1);
		return "(" . $sql . ")";

	}

	protected function processVALUES($parsed) {
		$sql = "";
		foreach ($parsed as $k => $v) {
			$len = strlen($sql);
			$sql .= $this->processRecord($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('VALUES', $k, $v, 'expr_type');
			}

			$sql .= ",";
		}
		$sql = substr($sql, 0, -1);
		return "VALUES " . $sql;
	}

	protected function processINSERT($parsed) {
		$sql = "INSERT INTO " . $parsed['table'];

		if ($parsed['columns'] === false) {
			return $sql;
		}

		$columns = "";
		foreach ($parsed['columns'] as $k => $v) {
			$len = strlen($columns);
			$columns .= $this->processColRef($v);

			if ($len == strlen($columns)) {
				throw new RDatabaseSqlparserExceptioncreatesql('INSERT[columns]', $k, $v, 'expr_type');
			}

			$columns .= ",";
		}

		if ($columns !== "") {
			$columns = " (" . substr($columns, 0, -1) . ")";
		}

		$sql .= $columns;
		return $sql;
	}

	protected function processUPDATE($parsed) {
		return "UPDATE " . $parsed[0]['table'];
	}

	protected function processSetExpression($parsed) {
		if ($parsed['expr_type'] !== 'expression') {
			return "";
		}
		$sql = "";
		foreach ($parsed['sub_tree'] as $k => $v) {
			$len = strlen($sql);
			if (($this->isReserved($v) || $this->isOperator($v)) && !empty($sql) && substr($sql, -1) == ',')
			{
				$sql = substr($sql, 0, -1) . ' ';
			}

			$sql .= $this->processReserved($v);
			$sql .= $this->processColRef($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processOperator($v);
			$sql .= $this->processFunction($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('SET expression subtree', $k, $v, 'expr_type');
			}

			$sql .= " ";
		}

		$sql = substr($sql, 0, -1);
		return $sql;
	}

	protected function processSET($parsed) {
		$sql = "";
		foreach ($parsed as $k => $v) {
			$len = strlen($sql);
			$sql .= $this->processSetExpression($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('SET', $k, $v, 'expr_type');
			}

			$sql .= ",";
		}
		return "SET " . substr($sql, 0, -1);
	}

	protected function processWHERE($parsed) {
		$sql = "WHERE ";
		foreach ($parsed as $k => $v) {
			$len = strlen($sql);

			if (($this->isReserved($v) || $this->isOperator($v)) && !empty($sql) && substr($sql, -1) == ',')
			{
				$sql = substr($sql, 0, -1) . ' ';
			}

			$sql .= $this->processReserved($v);
			$sql .= $this->processOperator($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processColRef($v);
			$sql .= $this->processSubquery($v);
			$sql .= $this->processInList($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processWhereExpression($v);
			$sql .= $this->processWhereBracketExpression($v);

			if (strlen($sql) == $len) {
				throw new RDatabaseSqlparserExceptioncreatesql('WHERE', $k, $v, 'expr_type');
			}

			$sql .= " ";
		}
		return substr($sql, 0, -1);
	}

	protected function processWhereExpression($parsed) {
		if (empty($parsed['expr_type']) || $parsed['expr_type'] !== 'expression') {
			return "";
		}
		$sql = "";
		foreach ($parsed['sub_tree'] as $k => $v) {
			$len = strlen($sql);
			if (($this->isReserved($v) || $this->isOperator($v)) && !empty($sql) && substr($sql, -1) == ',')
			{
				$sql = substr($sql, 0, -1) . ' ';
			}

			$sql .= $this->processReserved($v);
			$sql .= $this->processColRef($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processOperator($v);
			$sql .= $this->processInList($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processWhereExpression($v);
			$sql .= $this->processWhereBracketExpression($v);
			$sql .= $this->processSubQuery($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('WHERE expression subtree', $k, $v, 'expr_type');
			}

			$sql .= " ";
		}

		$sql = substr($sql, 0, -1);
		return $sql;
	}

	protected function processWhereBracketExpression($parsed) {
		if (empty($parsed['expr_type']) || $parsed['expr_type'] !== 'bracket_expression') {
			return "";
		}
		elseif (empty($parsed['sub_tree']))
		{
			return "()";
		}
		$sql = "";
		foreach ($parsed['sub_tree'] as $k => $v) {
			$len = strlen($sql);
			if (($this->isReserved($v) || $this->isOperator($v)) && !empty($sql) && substr($sql, -1) == ',')
			{
				$sql = substr($sql, 0, -1) . ' ';
			}

			$sql .= $this->processReserved($v);
			$sql .= $this->processColRef($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processOperator($v);
			$sql .= $this->processInList($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processWhereExpression($v);
			$sql .= $this->processWhereBracketExpression($v);
			$sql .= $this->processSubQuery($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('WHERE expression subtree', $k, $v, 'expr_type');
			}

			$sql .= " ";
		}

		$sql = "(" . substr($sql, 0, -1) . ")";
		return $sql;
	}

	protected function processOrderByAlias($parsed) {
		if ($parsed['expr_type'] !== 'alias') {
			return "";
		}
		return $parsed['base_expr'] . $this->processDirection($parsed['direction']);
	}

	protected function processLimitRowCount($key, $value) {
		if ($key != 'rowcount') {
			return "";
		}
		return $value;
	}

	protected function processLimitOffset($key, $value) {
		if ($key !== 'offset') {
			return "";
		}
		return $value;
	}

	protected function processFunction($parsed) {
		if (empty($parsed['expr_type']) || (($parsed['expr_type'] !== 'aggregate_function') && ($parsed['expr_type'] !== 'function'))) {
			return "";
		}

		if ($parsed['sub_tree'] === false) {
			return $parsed['base_expr'] . "()";
		}

		$sql = "";

		foreach ($parsed['sub_tree'] as $k => $v) {
			$len = strlen($sql);

			if (($this->isReserved($v) || $this->isOperator($v)) && !empty($sql) && substr($sql, -1) == ',')
			{
				$sql = substr($sql, 0, -1) . ' ';
			}

			$sql .= $this->processReserved($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processOperator($v);
			$sql .= $this->processColRef($v);
			$sql .= $this->processSelectBracketExpression($v);
			$sql .= $this->processSelectExpression($v);
			$sql .= $this->processSubQuery($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('function subtree', $k, $v, 'expr_type');
			}

			$sql .= ($this->isReserved($v) || $this->isOperator($v) ? " " : ",");
		}

		$sql2 = $parsed['base_expr'] . "(" . substr($sql, 0, -1) . ")";
		$sql2 .= !empty($parsed['alias']) ? $this->processAlias($parsed['alias']) : '';

		return $sql2;
	}

	protected function processSelectExpression($parsed) {
		if ($parsed['expr_type'] !== 'expression') {
			return "";
		}
		$sql = $this->processSubTree($parsed, " ");
		$sql .= $this->processAlias($parsed['alias']);
		return $sql;
	}

	protected function processGroupByExpression($parsed) {
		if ($parsed['expr_type'] !== 'expression') {
			return "";
		}
		$sql = $this->processSubTree($parsed, " ");
		return $sql;
	}

	protected function processOrderByExpression($parsed) {
		if ($parsed['expr_type'] !== 'expression') {
			return "";
		}
		$sql = $this->processSubTree($parsed, " ");

		return $sql;
	}

	protected function processSelectBracketExpression($parsed)
	{
		if (empty($parsed['expr_type']) || $parsed['expr_type'] !== 'bracket_expression')
		{
			return "";
		}
		elseif (empty($parsed['sub_tree']))
		{
			return "";
		}

		$sql = $this->processSubTree($parsed, " ");
		$sql .= $this->processColRef($parsed);
		$sql = "(" . $sql . ")";
		$sql .= !empty($parsed['alias']) ? $this->processAlias($parsed['alias']) : '';

		return $sql;
	}

	protected function processSubTree($parsed, $delim = " ") {
		if ($parsed['sub_tree'] === '') {
			return "";
		}
		$sql = "";
		foreach ($parsed['sub_tree'] as $k => $v) {
			$len = strlen($sql);

			if (($this->isReserved($v) || $this->isOperator($v)) && !empty($sql) && substr($sql, -1) == ',')
			{
				$sql = substr($sql, 0, -1) . ' ';
			}

			$sql .= $this->processReserved($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processOperator($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processSelectBracketExpression($v);
			$sql .= $this->processSelectExpression($v);
			$sql .= $this->processSubQuery($v);

			// Always last since it will give alias if needed
			$sql .= $this->processColRef($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('expression subtree', $k, $v, 'expr_type');
			}

			$sql .= ($this->isReserved($v) || $this->isOperator($v) ? " " : $delim);
		}

		$sql = substr($sql, 0, -1);

		return $sql;
	}

	protected function processRefClause($parsed) {
		if ($parsed === false) {
			return "";
		}

		$sql = "";
		foreach ($parsed as $k => $v) {
			$len = strlen($sql);

			if (($this->isReserved($v) || $this->isOperator($v)) && !empty($sql) && substr($sql, -1) == ',')
			{
				$sql = substr($sql, 0, -1) . ' ';
			}

			$sql .= $this->processReserved($v);
			$sql .= $this->processColRef($v);
			$sql .= $this->processFunction($v);
			$sql .= $this->processOperator($v);
			$sql .= $this->processInList($v);
			$sql .= $this->processConstant($v);
			$sql .= $this->processSelectBracketExpression($v);
			$sql .= $this->processSelectExpression($v);
			$sql .= $this->processSubQuery($v);

			if ($len == strlen($sql)) {
				throw new RDatabaseSqlparserExceptioncreatesql('expression ref_clause', $k, $v, 'expr_type');
			}

			$sql .= " ";
		}
		return "(" . substr($sql, 0, -1) . ")";
	}

	protected function processAlias($parsed) {
		if ($parsed === false) {
			return "";
		}
		$sql = "";
		if (!empty($parsed['as'])) {
			$sql .= " as";
		}
		if (!empty($parsed['name'])) {
		$sql .= " " . $parsed['name'];
		}

		return $sql;
	}

	protected function processJoin($parsed) {
		if ($parsed === 'CROSS') {
			return ",";
		}
		if ($parsed === 'JOIN') {
			return "INNER JOIN";
		}
		if ($parsed === 'LEFT') {
			return "LEFT JOIN";
		}
		if ($parsed === 'RIGHT') {
			return "RIGHT JOIN";
		}
		// TODO: add more
		throw new Exception($parsed, 20);
	}

	protected function processRefType($parsed) {
		if ($parsed === false) {
			return "";
		}
		if ($parsed === 'ON') {
			return " ON ";
		}
		if ($parsed === 'USING') {
			return " USING ";
		}
		// TODO: add more
		throw new Exception($parsed, 20);
	}

	protected function processTable($parsed, $index) {
		if ($parsed['expr_type'] !== 'table') {
			return "";
		}

		$sql = $parsed['table'];
		$sql .= $this->processAlias($parsed['alias']);

		if ($index !== 0) {
			$sql = $this->processJoin($parsed['join_type']) . " " . $sql;
			$sql .= $this->processRefType($parsed['ref_type']);
			$sql .= $this->processRefClause($parsed['ref_clause']);
		}
		return $sql;
	}

	protected function processTableExpression($parsed, $index) {
		if ($parsed['expr_type'] !== 'table_expression') {
			return "";
		}
		$sql = substr($this->processFROM($parsed['sub_tree']), 5); // remove FROM keyword
		$sql = "(" . $sql . ")";

		if (isset($parsed['alias'])) {
			$sql .= $this->processAlias($parsed['alias']);
		}

		if ($index !== 0) {
			$sql = $this->processJoin($parsed['join_type']) . " " . $sql;
			$sql .= $this->processRefType($parsed['ref_type']);
			$sql .= $this->processRefClause($parsed['ref_clause']);
		}
		return $sql;
	}

	protected function processSubQuery($parsed, $index = 0)
	{
		if ($parsed['expr_type'] !== 'subquery')
		{
			return "";
		}

		// We handle subqueries in a loop in sqltranslation file
		$sql = $parsed['base_expr'];
		$sql = "(" . $sql . ")";

		if (isset($parsed['alias']))
		{
			$sql .= $this->processAlias($parsed['alias']);
		}

		if ($index !== 0)
		{
			$sql = $this->processJoin($parsed['join_type']) . " " . $sql;
			$sql .= $this->processRefType($parsed['ref_type']);
			$sql .= $this->processRefClause($parsed['ref_clause']);
		}

		return $sql;
	}

	protected function processOperator($parsed) {
		if (empty($parsed['expr_type']) || $parsed['expr_type'] !== 'operator') {
			return "";
		}
		return $parsed['base_expr'];
	}

	protected function processColRef($parsed) {
		if (empty($parsed['expr_type']) || $parsed['expr_type'] !== 'colref') {
			return "";
		}
		$sql = $parsed['base_expr'];
		if (isset($parsed['alias'])) {
			$sql .= $this->processAlias($parsed['alias']);
		}
		if (isset($parsed['direction'])) {
			$sql .= $this->processDirection($parsed['direction']);
		}
		return $sql;
	}

	protected function processDirection($parsed) {
		$sql = ($parsed ? " " . $parsed : "");
		return $sql;
	}

	protected function processReserved($parsed) {
		if (!$this->isReserved($parsed)) {
			return "";
		}
		return $parsed['base_expr'];
	}

	protected function isReserved($parsed) {
		return (!empty($parsed['expr_type']) && $parsed['expr_type'] === 'reserved');
	}

	protected function isOperator($parsed) {
		return (!empty($parsed['expr_type']) && $parsed['expr_type'] === 'operator');
	}

	protected function processConstant($parsed) {
		if (empty($parsed['expr_type']) || $parsed['expr_type'] !== 'const') {
			return "";
		}

		$sql = $parsed['base_expr'];

		if (isset($parsed['alias'])) {
			$sql .= $this->processAlias($parsed['alias']);
		}

		return $sql;
	}

	protected function processInList($parsed) {
		if (empty($parsed['expr_type']) || $parsed['expr_type'] !== 'in-list') {
			return "";
		}
		$sql = $this->processSubTree($parsed, ",");
		return "(" . $sql . ")";
	}

}
