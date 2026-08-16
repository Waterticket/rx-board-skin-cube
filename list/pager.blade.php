{{-- 페이지네이션. paging_mode 와 무관하게 항상 렌더한다. more/infinite 모드에서는 js/cube.list.js 가 부팅 후 hidden 속성으로 감춘다(DOM에서 제거하지 않는다). --}}
{{-- 속성을 통째로 찍을 때는 {!! !!} 여야 한다. {{ }} 는 따옴표까지 이스케이프해서 data-js-hide="pager" 라는 이름의 빈 속성이 되고, JS 셀렉터가 영영 못 잡는다(무한 스크롤인데 페이지네이션이 계속 보이던 원인). --}}
<div class="cube-pager-wrap"{!! $cube['pagingMode'] !== 'paging' ? ' data-js-hide="pager"' : '' !!}>
	@include('../ui/pagination', ['nav' => $page_navigation ?? null, 'param' => 'page', 'resetDocSrl' => true])
</div>
<p class="cube-loadend" data-role="loadend" hidden aria-live="polite">마지막 글입니다.</p>
