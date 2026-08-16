{{-- 새 댓글 작성(항상 parent_srl=0). 답글은 comment/item.blade.php의 '답글' 링크가 dispBoardReplyComment 전체 페이지로 보낸다. 제출은 board.procBoardInsertComment 를 exec_json 으로 호출한다(js/cube.action.js). --}}
@php
	$cube_canComment = ($grant->write_comment ?? false) && $oDocument->allowComment();
@endphp
@if($cube_canComment)
<form class="cube-comment-form" id="cube-comment-form" data-document-srl="{{ $oDocument->document_srl }}">
	<textarea name="content" class="cube-comment-input" placeholder="댓글을 남겨주세요." rows="2" required></textarea>
	@if(!$is_logged)
		<div class="cube-comment-guest">
			<input type="text" name="nick_name" class="cube-comment-guest-input" placeholder="닉네임" required />
			<input type="password" name="password" class="cube-comment-guest-input" placeholder="비밀번호" required />
		</div>
	@endif
	<div class="cube-comment-form-foot">
		@if($cube['useSticker'])
			<button type="button" class="cube-sticker-btn" data-role="sticker-toggle" aria-expanded="false">{!! $cube_icons['smile'] !!}스티커</button>
		@endif
		<span class="cube-comment-form-msg" data-role="form-msg" aria-live="polite"></span>
		<button type="submit" class="cube-btn cube-btn-primary">등록</button>
	</div>
	@if($cube['useSticker'])
		{{-- 내용은 js/cube.sticker.js 가 열릴 때 채운다. 서버는 빈 껍데기만 내보낸다 — 스티커 목록은 회원별(구매 목록)이라 캐시가 걸리면 안 된다. --}}
		<div class="cube-sticker" data-role="sticker-panel" hidden></div>
	@endif
</form>
@elseif(!$oDocument->allowComment())
	<p class="cube-comment-closed">댓글이 잠긴 글입니다.</p>
@elseif(!$is_logged)
	<p class="cube-comment-closed">댓글을 작성하려면 <a href="{{ getUrl('', 'act', 'dispMemberLoginForm') }}">로그인</a>이 필요합니다.</p>
@else
	<p class="cube-comment-closed">댓글 작성 권한이 없습니다.</p>
@endif
