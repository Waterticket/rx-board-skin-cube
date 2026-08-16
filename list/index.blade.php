{{-- 목록 화면 조립. list.blade.php가 $oDocument->isExists()===false 일 때 include 한다. 경로는 이 파일이 있는 list/ 기준. --}}
{{-- 공지는 첫 페이지에만 붙인다. board.view.php:532 는 페이지와 무관하게 매번 같은 공지를 내려주므로, 그대로 두면 무한 스크롤/더 보기로 다음 페이지를 이어 붙일 때마다 같은 공지가 다시 나온다. 첫 페이지에서는 page 가 null 로 오는 경우가 있어(board.view.php:654) 0 이하를 전부 첫 페이지로 본다. --}}
@php
	$cube_showNotice = (int)($page ?? 0) <= 1;
@endphp
<div class="cube-wrap">
	@include('head')
	@include('categories')
	@include('toolbar')

	@if($document_list || ($cube_showNotice && $notice_list))
		@include('grid')
		<div class="sr-only" aria-live="polite" data-role="announce"></div>
		@include('loadmore')
		@include('pager')
	@else
		@include('empty')
	@endif

	@include('search')
</div>
