{{-- '더 보기' 버튼과 무한 스크롤 센티널. 기본은 hidden — js/cube.list.js가 paging_mode에 맞는 쪽만 켠다. JS가 실행되지 않으면 계속 숨겨진 채로 남고 list/pager.blade.php의 페이지네이션이 유일한 이동 수단이 된다. --}}
@if($cube_hasNext && $cube['pagingMode'] !== 'paging')
	<button type="button" class="cube-loadmore-btn" data-role="loadmore" hidden>더 보기</button>
	<div class="cube-sentinel" data-role="sentinel" aria-hidden="true" hidden></div>
@endif
