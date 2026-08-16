{{-- 정렬 + 관리 + 글쓰기. 링크만으로 동작한다(체크박스 담기는 코어의 doAddDocumentCart 가 처리). --}}
{{-- 관리 버튼을 목록 하단이 아니라 여기 위쪽에 두는 이유: 페이징 모드가 무한 스크롤이면 하단 영역이 화면에 나타나지 않을 수 있다. --}}
<div class="cube-toolbar">
	<nav class="cube-sort" aria-label="정렬">
		<a class="cube-sort-link{{ $cube_currentSort === 'list_order' ? ' is-active' : '' }}" href="{{ $cube_sortLinks['list_order'] }}">최신순</a>
		<a class="cube-sort-link{{ $cube_currentSort === 'voted_count' ? ' is-active' : '' }}" href="{{ $cube_sortLinks['voted_count'] }}">추천순</a>
		<a class="cube-sort-link{{ $cube_currentSort === 'comment_count' ? ' is-active' : '' }}" href="{{ $cube_sortLinks['comment_count'] }}">댓글순</a>
	</nav>

	<div class="cube-toolbar-actions">
		@if($cube['isManager'])
			{{-- 관리 도구는 글쓰기보다 한 단 낮은 위계로 둔다 — 테두리 없는 작은 텍스트 버튼. --}}
			<div class="cube-manage-tools">
				<label class="cube-manage-btn cube-checkall">
					<input type="checkbox" data-role="cart-checkall" />
					<span>전체 선택</span>
				</label>
				<a class="cube-manage-btn" href="{{ getUrl('', 'mid', $mid, 'act', 'dispBoardAdminBoardInfo') }}">{!! $cube_icons['gear'] !!}<span>설정</span></a>
				{{-- 게시물 관리 화면은 세션에 담긴 목록(document.view.php:86 의 $_SESSION['document_management'])을 읽으므로 팝업으로 띄운다 — 목록 페이지를 떠나지 않아야 체크 상태를 이어서 볼 수 있다. --}}
				<a class="cube-manage-btn" href="{{ getUrl('', 'mid', $mid, 'act', 'dispDocumentManageDocument') }}" onclick="popopen(this.href, 'manageDocument'); return false;">{!! $cube_icons['check-square'] !!}<span>게시물 관리</span></a>
			</div>
		@endif

		@if($grant->write_document ?? false)
			<a class="cube-btn cube-btn-primary cube-write-btn" href="{{ getUrl('', 'mid', $mid, 'act', 'dispBoardWrite') }}">
				<span aria-hidden="true">＋</span> 글쓰기
			</a>
		@endif
	</div>
</div>
