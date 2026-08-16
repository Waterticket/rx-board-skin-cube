{{-- 본문. getContent() 는 비밀글이면 이미 안내 문구로 대체해서 돌려준다. --}}
<div class="cube-read-body{{ !$oDocument->isAccessible() ? ' is-secret' : '' }}">
	{!! $oDocument->getContent(false) !!}
</div>
