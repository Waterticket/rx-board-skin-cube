{{-- 댓글 목록 + 대댓글 그룹핑. comments 테이블에는 depth 컬럼이 없고 parent_srl만 있다(schemas/comments.xml). $comment_list는 이미 부모-자식이 인접하도록 정렬되어 오므로 comment_srl->depth 맵을 메모리에서만 계산한다(추가 쿼리 없음). 반복문 안의 include는 댓글 1건당 1회만. --}}
@if(!empty($comment_list))
	@php
		$cube_depthMap = [];
		foreach ($comment_list as $cube_dc) {
			$cube_dp = (int)$cube_dc->get('parent_srl');
			$cube_depthMap[$cube_dc->comment_srl] = ($cube_dp && isset($cube_depthMap[$cube_dp])) ? min($cube_depthMap[$cube_dp] + 1, 2) : ($cube_dp ? 1 : 0);
		}
		$cube_replyOpen = false;
	@endphp
	<ul class="cube-comment-list">
		@foreach($comment_list as $comment)
			@php
				$isReply = (int)$comment->get('parent_srl') > 0;
				$cube_depth = $cube_depthMap[$comment->comment_srl] ?? 0;
			@endphp
			@if($isReply)
				@if(!$cube_replyOpen)
					<li class="cube-reply-group">
					@php
						$cube_replyOpen = true;
					@endphp
				@endif
				@include('item')
			@else
				@if($cube_replyOpen)
					</li>
					@php
						$cube_replyOpen = false;
					@endphp
				@endif
				<li>
					@include('item')
				</li>
			@endif
		@endforeach
		@if($cube_replyOpen)
			</li>
		@endif
	</ul>
@endif
