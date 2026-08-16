{{-- board.view.php:1052 부근 dispBoardDelete() -> setTemplateFile('delete_form'). --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap cube-form-wrap cube-confirm-wrap">
	<form action="./" method="get" class="cube-confirm" onsubmit="return procFilter(this, delete_document)">
		<input type="hidden" name="mid" value="{{ $mid }}" />
		<input type="hidden" name="page" value="{{ $page }}" />
		<input type="hidden" name="document_srl" value="{{ $document_srl }}" />
		<h1 class="cube-confirm-title">글을 삭제할까요?</h1>
		<p class="cube-confirm-body">{!! $oDocument->getTitle() !!}</p>
		<div class="cube-form-foot">
			<button type="button" class="cube-btn" onclick="history.back()">취소</button>
			<button type="submit" class="cube-btn cube-btn-primary">삭제</button>
		</div>
	</form>
</div>

@include('layout/close')
