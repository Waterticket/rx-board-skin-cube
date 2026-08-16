{{-- 추천 · 댓글 이동 · 공유. getMyVote() 는 상세 페이지이므로 여기서 1회만 호출한다. --}}
@php
	$cube_voted = ($cube['showVoteBox'] && ($board_features->document->vote_up ?? false)) ? $oDocument->getMyVote() : false;
@endphp
<div class="cube-read-actions">
	@if($cube['showVoteBox'] && ($board_features->document->vote_up ?? false))
		<button type="button" class="cube-vote-btn{{ $cube_voted ? ' is-active' : '' }}" data-srl="{{ $oDocument->document_srl }}" aria-pressed="{{ $cube_voted ? 'true' : 'false' }}" aria-label="추천">
			{!! $cube_icons['heart'] !!}
			<span data-role="vote-count">{{ (int)$oDocument->get('voted_count') }}</span>
		</button>
	@endif

	<a class="cube-jump-comment" href="#comment" aria-label="댓글로 이동">
		{!! $cube_icons['comment'] !!}
		<span>{{ (int)$oDocument->getCommentCount() }}</span>
	</a>

	<div class="cube-share">
		<button type="button" class="cube-btn cube-copy-btn" data-url="{{ $oDocument->getPermanentUrl() }}">
			{!! $cube_icons['link'] !!}<span data-role="copy-label">링크 복사</span>
		</button>
		@if($cube['useSocialShare'])
			<a class="cube-btn" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url={{ rawurlencode($oDocument->getPermanentUrl()) }}&text={{ rawurlencode($oDocument->getTitleText()) }}">X</a>
			<a class="cube-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($oDocument->getPermanentUrl()) }}">Facebook</a>
		@endif
	</div>
</div>
