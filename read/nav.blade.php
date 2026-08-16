{{-- 목록으로. 이전/다음 글 이동은 넣지 않았다 — 코어에 prev/next 조회 API가 없고, 스킨은 PHP/쿼리를 추가할 수 없어 커스텀 쿼리로 구현할 수 없다. 02_라이믹스_적용_명세.md §15 참고. --}}
<div class="cube-read-nav">
	<a class="cube-btn" href="{{ getUrl('document_srl', '', 'page', '') }}">
		<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 8h11M2.5 8l4-4M2.5 8l4 4"/></svg>
		목록으로
	</a>
</div>
