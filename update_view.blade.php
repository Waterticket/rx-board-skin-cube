{{-- board.view.php:1543 dispBoardUpdateLogView() -> setTemplateFile('update_view'). --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap cube-read-wrap">
	<p class="cube-update-meta">{{ $update_log->update_nick_name }} · {{ zdate($update_log->regdate, 'Y.m.d H:i') }}</p>
	<h1 class="cube-read-title">{!! htmlspecialchars($update_log->title) !!}</h1>
	@if(!empty($update_log->reason_update))
		<p class="cube-update-reason">수정 사유: {{ $update_log->reason_update }}</p>
	@endif
	<div class="cube-read-body">{!! $update_log->content !!}</div>

	@if(!empty($extra_vars))
		<dl class="cube-update-extra">
			@foreach($extra_vars as $cube_ev_key => $cube_ev_html)
				<dt>{{ $cube_ev_key }}</dt>
				<dd>{!! $cube_ev_html !!}</dd>
			@endforeach
		</dl>
	@endif

	<div class="cube-form-foot">
		<button type="button" class="cube-btn" onclick="history.back()">돌아가기</button>
	</div>
</div>

@include('layout/close')
