{{-- board.view.php:1620 dispBoardVoteLog() -> setTemplateFile('vote_log'). --}}
@include('lib/boot')
@load('css/00-tokens.css')
@load('css/10-base.css')
@load('css/60-form.css')
<div class="cube">
	<div class="cube-vote-log">
		@if(!empty($vote_member_info))
			<h2 class="cube-vote-log-title">추천</h2>
			<ul class="cube-vote-log-list">
				@foreach($vote_member_info as $cube_vm)
					<li>@if($cube_vm->member_srl > 0)<a class="member_{{ (int)$cube_vm->member_srl }}" href="#popup_menu_area" onclick="return false">{{ $cube_vm->nick_name }}</a>@else{{ $cube_vm->nick_name }}@endif</li>
				@endforeach
			</ul>
		@endif
		@if(!empty($blame_member_info))
			<h2 class="cube-vote-log-title">비추천</h2>
			<ul class="cube-vote-log-list">
				@foreach($blame_member_info as $cube_bm)
					<li>@if($cube_bm->member_srl > 0)<a class="member_{{ (int)$cube_bm->member_srl }}" href="#popup_menu_area" onclick="return false">{{ $cube_bm->nick_name }}</a>@else{{ $cube_bm->nick_name }}@endif</li>
				@endforeach
			</ul>
		@endif
		@if(empty($vote_member_info) && empty($blame_member_info))
			<p class="cube-empty">기록이 없습니다.</p>
		@endif
	</div>
</div>
