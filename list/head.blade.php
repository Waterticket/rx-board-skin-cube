{{-- 게시판 제목 + 설명. --}}
@if($cube['showBoardTitle'])
<div class="cube-head">
	<h1 class="cube-head-title">{{ $module_info->browser_title }}</h1>
	@if($cube['boardDescription'])
		<p class="cube-head-desc">{{ $cube['boardDescription'] }}</p>
	@endif
</div>
@endif
