{{-- board.view.php dispBoardDeleteComment() -> setTemplateFile('delete_comment_form'). $oDocument는 세팅되지 않으므로 $oComment만 쓴다. --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap cube-form-wrap cube-confirm-wrap">
	<form action="./" method="get" class="cube-confirm" onsubmit="return procFilter(this, delete_comment)">
		<input type="hidden" name="mid" value="{{ $mid }}" />
		<input type="hidden" name="page" value="{{ $page }}" />
		<input type="hidden" name="document_srl" value="{{ $oComment->get('document_srl') }}" />
		<input type="hidden" name="comment_srl" value="{{ $oComment->get('comment_srl') }}" />
		<h1 class="cube-confirm-title">댓글을 삭제할까요?</h1>
		@if($oComment->isExists())
			<p class="cube-confirm-body">{!! $oComment->getContent(false) !!}</p>
		@endif
		<div class="cube-form-foot">
			<button type="button" class="cube-btn" onclick="history.back()">취소</button>
			<button type="submit" class="cube-btn cube-btn-primary">삭제</button>
		</div>
	</form>
</div>

@include('layout/close')
