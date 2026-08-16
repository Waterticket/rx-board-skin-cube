{{-- board.view.php:1506 dispBoardUpdateLog() -> setTemplateFile('update_list'). 관리자 전용 수정 이력. --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap">
	<h1 class="cube-form-title">수정 이력</h1>
	@if(!empty($updatelog->data))
		<ul class="cube-update-list">
			@foreach($updatelog->data as $cube_log)
				<li>
					<a href="{{ getUrl('', 'mid', $mid, 'act', 'dispBoardUpdateLogView', 'update_id', $cube_log->update_id) }}">
						{{ $cube_log->title }}
					</a>
					<span class="cube-update-meta">{{ $cube_log->update_nick_name }} · {{ zdate($cube_log->regdate, 'Y.m.d H:i') }}</span>
				</li>
			@endforeach
		</ul>
		@include('ui/pagination', ['nav' => $page_navigation ?? null, 'param' => 'page'])
	@else
		<p class="cube-empty">수정 이력이 없습니다.</p>
	@endif
</div>

@include('layout/close')
