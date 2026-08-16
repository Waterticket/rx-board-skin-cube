{{-- 공지 + 일반 글 카드 그리드. 반복문 안의 include는 카드 1건당 1회만 (02문서 §3.5). --}}
{{-- $cube_showNotice 는 list/index.blade.php 가 미리 정한다. --}}
<div class="cube-list" id="cube-list" data-next-url="{{ $cube_nextUrl }}">
	@foreach(($cube_showNotice ? ($notice_list ?? []) : []) as $document)
		@php
			$isNotice = true;
		@endphp
		@include('card')
	@endforeach
	@foreach(($document_list ?? []) as $no => $document)
		@php
			$isNotice = false;
		@endphp
		@include('card')
	@endforeach
</div>
