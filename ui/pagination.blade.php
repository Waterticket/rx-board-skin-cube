{{-- 재사용 leaf 컴포넌트. 호출 지점이 list/pager · comment/pager 2곳뿐이라 반복 include 비용 문제가 없다. --}}
{{-- 명시적으로 vars를 받으므로($nav, $param, $resetDocSrl) 전역 상태는 보이지 않는다 — 그래서 아이콘도 자체 내장한다. --}}
{{-- 사용: @include('ui/pagination', ['nav' => $page_navigation, 'param' => 'page', 'resetDocSrl' => true]) --}}
{{-- comment 페이지네이션(cpage)은 document_srl을 유지해야 하므로 resetDocSrl을 넘기지 않는다. --}}
{{-- 클로저/화살표 함수는 쓰지 않는다 — v2 파서의 변수 스코프 자동 변환이 fn(...) 매개변수까지 바꿔 컴파일된 PHP를 깨뜨릴 수 있다. 그래서 조건을 각 자리에 그대로 반복한다. --}}
@if(!empty($nav) && $nav->last_page > 1)
<nav class="cube-pagination" aria-label="페이지">
	@if($nav->cur_page > 1)
		<a class="cube-page-link cube-page-prev" href="{{ !empty($resetDocSrl) ? getUrl('document_srl', '', $param, $nav->cur_page - 1) : getUrl($param, $nav->cur_page - 1) }}" aria-label="이전 페이지">
			<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m10 3-5 5 5 5"/></svg>
		</a>
	@endif
	@foreach($nav as $cube_page_no)
		<a class="cube-page-link{{ $nav->cur_page == $cube_page_no ? ' is-active' : '' }}"
			href="{{ !empty($resetDocSrl) ? getUrl('document_srl', '', $param, $cube_page_no) : getUrl($param, $cube_page_no) }}"
			@if($nav->cur_page == $cube_page_no) aria-current="page" @endif
		>{{ $cube_page_no }}</a>
	@endforeach
	@if($nav->cur_page < $nav->last_page)
		<a class="cube-page-link cube-page-next" href="{{ !empty($resetDocSrl) ? getUrl('document_srl', '', $param, $nav->cur_page + 1) : getUrl($param, $nav->cur_page + 1) }}" aria-label="다음 페이지">
			<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 3 5 5-5 5"/></svg>
		</a>
	@endif
</nav>
@endif
