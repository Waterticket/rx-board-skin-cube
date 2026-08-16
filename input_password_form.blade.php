{{-- 비회원 글/댓글 수정·삭제 시 비밀번호 확인. write_form.blade.php:932, delete_comment 경로 등에서 setTemplateFile('input_password_form')로 진입한다. --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap cube-form-wrap cube-confirm-wrap">
	<form action="./" method="get" class="cube-confirm" onsubmit="return procFilter(this, input_password)">
		<input type="hidden" name="mid" value="{{ $mid }}" />
		<input type="hidden" name="page" value="{{ $page }}" />
		<input type="hidden" name="document_srl" value="{{ $document_srl }}" />
		<input type="hidden" name="comment_srl" value="{{ $comment_srl }}" />
		<h1 class="cube-confirm-title">비밀번호 확인</h1>
		<div class="cube-form-row">
			<input type="password" name="password" id="cpw" class="cube-form-input" placeholder="비밀번호" required />
		</div>
		<div class="cube-form-foot">
			<button type="button" class="cube-btn" onclick="history.back()">취소</button>
			<button type="submit" class="cube-btn cube-btn-primary">확인</button>
		</div>
	</form>
</div>

@include('layout/close')
