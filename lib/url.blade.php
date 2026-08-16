{{-- 출력 없음. 정렬 · 페이지 URL을 미리 만들어 둔다. getUrl()은 현재 요청 인자를 유지한 채 지정한 키만 바꾼다. --}}
@php
	$cube_page = max(1, (int)($page ?: 1));
	$cube_totalPage = max(1, (int)($total_page ?: 1));
	$cube_hasNext = $cube_page < $cube_totalPage;

	// 상세 화면에서 다음 목록 페이지로 넘어갈 때는 document_srl을 반드시 제거한다.
	$cube_nextUrl = $cube_hasNext ? getUrl('document_srl', '', 'page', $cube_page + 1) : '';
	$cube_prevPageUrl = $cube_page > 1 ? getUrl('document_srl', '', 'page', $cube_page - 1) : '';

	$cube_currentSort = Context::get('sort_index') ?: (($module_info->order_target ?? '') ?: 'list_order');

	/*
		list_order 만 order_type 이 asc 다 — 오타가 아니다.
		이 컬럼은 글을 넣을 때 document_srl(또는 시퀀스)에 -1 을 곱해 저장한다
		(document.controller.php:780, :1100). 최신 글일수록 값이 더 작은 음수라서
		오름차순이 곧 최신순이고, desc 로 주면 가장 오래된 글부터 나온다.
		voted_count / comment_count 는 그냥 양수 카운터라 desc 가 맞다.

		최신순은 게시판 기본 정렬과 같으면 인자를 아예 붙이지 않는다 — 주소가 깨끗해진다.
		다만 관리자가 게시판 기본 정렬을 추천순 같은 걸로 바꿔 놨다면 인자를 빼는 순간
		'최신순' 링크가 최신순이 아니게 되므로, 그때는 명시적으로 붙인다
		(코어가 보는 기본값: board.view.php:641,645).
	*/
	$cube_defaultIsRecent = ((($module_info->order_target ?? '') ?: 'list_order') === 'list_order')
		&& ((($module_info->order_type ?? '') ?: 'asc') === 'asc');

	$cube_sortLinks = [
		'list_order'    => $cube_defaultIsRecent
			? getUrl('document_srl', '', 'sort_index', '', 'order_type', '', 'page', '')
			: getUrl('document_srl', '', 'sort_index', 'list_order', 'order_type', 'asc', 'page', ''),
		'voted_count'   => getUrl('document_srl', '', 'sort_index', 'voted_count', 'order_type', 'desc', 'page', ''),
		'comment_count' => getUrl('document_srl', '', 'sort_index', 'comment_count', 'order_type', 'desc', 'page', ''),
	];

	$cube_allUrl = getUrl('category', '', 'document_srl', '', 'page', '');
@endphp
