{{-- board.view.php dispBoardWriteComment/dispBoardReplyComment/dispBoardModifyComment 공용 진입점. dispBoardModifyComment()는 $oDocument를 세팅하지 않으므로 이 템플릿에서는 $oDocument를 쓰지 않는다. --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

@php
	$cube_isEdit = $oComment->isExists();
	$cube_isReply = !$cube_isEdit && $oSourceComment->isExists();
@endphp

<div class="cube-wrap cube-form-wrap">
	<h1 class="cube-form-title">
		{{ $cube_isEdit ? '댓글 수정' : ($cube_isReply ? '답글 작성' : '댓글 작성') }}
	</h1>

	@if($cube_isReply)
		<div class="cube-quote">
			<span class="cube-quote-author">{{ $oSourceComment->getNickName() }}</span>
			<div class="cube-quote-body">{!! $oSourceComment->getContent(false) !!}</div>
		</div>
	@endif

	<form action="./" method="post" class="cube-form" onsubmit="return procFilter(this, insert_comment)">
		<input type="hidden" name="mid" value="{{ $mid }}" />
		<input type="hidden" name="document_srl" value="{{ $oComment->get('document_srl') }}" />
		<input type="hidden" name="comment_srl" value="{{ $oComment->get('comment_srl') }}" />
		<input type="hidden" name="parent_srl" value="{{ $oComment->get('parent_srl') }}" />
		{{-- write_form.blade.php와 같은 이유로 필요하다 -- 에디터 스킨 JS가 같은 폼에서 name=content 필드를 찾아 초기값으로 쓴다. --}}
		<input type="hidden" name="content" value="{!! $oComment->getContentText() !!}" />

		@if(!$is_logged)
			<div class="cube-form-row cube-form-row-split">
				<input type="text" name="nick_name" class="cube-form-input" value="{{ $oComment->get('nick_name') }}" placeholder="닉네임" />
				<input type="password" name="password" class="cube-form-input" placeholder="비밀번호" />
			</div>
		@endif

		<div class="cube-form-row cube-form-editor">
			{!! $oComment->getEditor() !!}
		</div>

		<div class="cube-form-row cube-form-options">
			@if($is_logged)
				<label><input type="checkbox" name="notify_message" value="Y" @checked(($module_info->notify_comment ?? 'N') === 'Y' || $oComment->get('notify_message') === 'Y') /> 답글 알림</label>
			@endif
			@if(($module_info->secret ?? 'N') === 'Y')
				<label><input type="checkbox" name="is_secret" value="Y" @checked($oComment->get('is_secret') === 'Y') /> 비밀 댓글</label>
			@endif
		</div>

		<div class="cube-form-foot">
			<button type="button" class="cube-btn" onclick="history.back()">취소</button>
			<button type="submit" class="cube-btn cube-btn-primary">{{ $cube_isEdit ? '수정' : '등록' }}</button>
		</div>
	</form>
</div>

@include('layout/close')
