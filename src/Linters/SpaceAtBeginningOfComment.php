<?php

namespace Glhd\LaraLint\Linters;

use Glhd\LaraLint\Contracts\Matcher;
use Glhd\LaraLint\Linters\Strategies\MatchingLinter;
use Glhd\LaraLint\Result;
use Illuminate\Support\Collection;
use Microsoft\PhpParser\Node;

class SpaceAtBeginningOfComment extends MatchingLinter
{
	protected function matcher() : Matcher
	{
		return $this->treeMatcher()
			->withChild(function(Node $node) {
				$comment = $node->getLeadingCommentAndWhitespaceText();
				return preg_match('/(?:^\s*(?:\/\/\S|\/\*\S)|^\s*\/\*.*\S\*\/\s*$)/m', $comment);
			});
	}
	
	protected function onMatch(Collection $nodes) : ?Result
	{
		return new Result($this, $nodes->first(), 'Please add spacing to comment.');
	}
}
