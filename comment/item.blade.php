{{-- 댓글 1건. comment/list.blade.php 에서 항목당 1회만 include 된다($comment, $isReply, $cube_depth 는 list가 미리 심어 둔다). 여기서 <li>는 렌더하지 않는다 — 부모가 그룹 단위로 감싼다. --}}
@php
	$cube_isAuthorReply = $comment->get('member_srl') > 0 && (int)$comment->get('member_srl') === (int)$oDocument->get('member_srl');
	$cube_cVoted = ($board_features->comment->vote_up ?? false) ? $comment->getMyVote() : false;
@endphp
<div class="cube-comment-item{{ $isReply ? ' is-reply depth-' . $cube_depth : '' }}" id="comment_{{ $comment->comment_srl }}">
	@if($comment->isDeleted())
		<p class="cube-comment-deleted">{!! $comment->getContent(false) !!}</p>
	@else
		<div class="cube-comment-head">
			@if($comment->get('member_srl') > 0)
				<a class="cube-comment-author member_{{ (int)$comment->get('member_srl') }}" href="#popup_menu_area" onclick="return false">{{ $comment->getNickName() }}</a>
			@else
				<span class="cube-comment-author">{{ $comment->getNickName() }}</span>
			@endif
			@if($cube_isAuthorReply)
				<span class="cube-comment-author-badge">작성자</span>
			@endif
		</div>
		<div class="cube-comment-body">{!! $comment->getContent(false) !!}</div>
		<div class="cube-comment-meta">
			<time datetime="{{ $comment->getRegdateDT() }}">{{ $comment->getRegdate('Y.m.d H:i') }}</time>

			@if($board_features->comment->vote_up ?? false)
				<button type="button" class="cube-comment-vote{{ $cube_cVoted ? ' is-active' : '' }}" data-srl="{{ $comment->comment_srl }}" aria-pressed="{{ $cube_cVoted ? 'true' : 'false' }}" aria-label="댓글 추천">
					{!! $cube_icons['heart'] !!}<span data-role="vote-count">{{ (int)$comment->get('voted_count') }}</span>
				</button>
			@endif

			@if($cube_depth < 2 && ($grant->write_comment ?? false))
				<button type="button" class="cube-comment-reply-btn" data-srl="{{ $comment->comment_srl }}" aria-expanded="false">
					{!! $cube_icons['reply'] !!}답글
				</button>
			@endif

			<details class="cube-dropdown cube-comment-dropdown">
				<summary class="cube-dropdown-trigger" aria-label="더보기">{!! $cube_icons['dots'] !!}</summary>
				<ul class="cube-dropdown-menu" role="menu">
					@if($board_features->comment->report ?? false)
						<li role="none"><button type="button" role="menuitem" class="cube-comment-report-btn" data-srl="{{ $comment->comment_srl }}">신고하기</button></li>
					@endif
					@if($comment->isGranted())
						<li role="none"><a role="menuitem" href="{{ getUrl('', 'mid', $mid, 'act', 'dispBoardModifyComment', 'comment_srl', $comment->comment_srl) }}">수정</a></li>
						<li role="none"><a role="menuitem" href="{{ getUrl('', 'mid', $mid, 'act', 'dispBoardDeleteComment', 'comment_srl', $comment->comment_srl) }}">삭제</a></li>
					@endif
				</ul>
			</details>
		</div>
		{{-- '답글' 클릭 시 js/cube.action.js가 이 자리에 인라인 답글 폼을 만들어 넣는다(새 페이지로 이동하지 않음). --}}
		<div class="cube-reply-form-slot" data-comment-srl="{{ $comment->comment_srl }}"></div>
	@endif
</div>
