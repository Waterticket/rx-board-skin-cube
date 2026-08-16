{{-- board.view.php:1478 dispBoardDeleteTrackback() -> setTemplateFile('delete_trackback_form'). 트랙백은 레거시 기능이라 최소 구현만 한다. --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap cube-form-wrap cube-confirm-wrap">
	<form action="./" method="get" class="cube-confirm" onsubmit="return procFilter(this, delete_trackback)">
		<input type="hidden" name="mid" value="{{ $mid }}" />
		<input type="hidden" name="document_srl" value="{{ $document_srl }}" />
		<input type="hidden" name="trackback_srl" value="{{ Context::get('trackback_srl') }}" />
		<h1 class="cube-confirm-title">트랙백을 삭제할까요?</h1>
		<div class="cube-form-foot">
			<button type="button" class="cube-btn" onclick="history.back()">취소</button>
			<button type="submit" class="cube-btn cube-btn-primary">삭제</button>
		</div>
	</form>
</div>

@include('layout/close')
