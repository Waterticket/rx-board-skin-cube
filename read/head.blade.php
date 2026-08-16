{{-- 상세 헤더: 브레드크럼 + 제목 + 작성자 + 메타. --}}
@php
	$cube_catTitle = null;
	if ($oDocument->get('category_srl') && isset($category_list[$oDocument->get('category_srl')])) {
		$cube_catTitle = $category_list[$oDocument->get('category_srl')]->title;
	}
	$cube_profileImg = $oDocument->getProfileImage();
@endphp
<div class="cube-read-head">
	<nav class="cube-breadcrumb" aria-label="위치">
		<a href="{{ getUrl('', 'mid', $mid) }}">{{ $module_info->browser_title }}</a>
		@if($cube_catTitle)
			<span aria-hidden="true">›</span>
			<a href="{{ getUrl('category', $oDocument->get('category_srl'), 'document_srl', '', 'page', '') }}">{{ $cube_catTitle }}</a>
		@endif
	</nav>

	<h1 class="cube-read-title">
		@if($oDocument->isNotice())<span class="cube-badge is-notice">공지</span>@endif
		{!! $oDocument->getTitle() !!}
	</h1>

	<div class="cube-read-byline">
		@if($cube_profileImg)
			<img class="cube-avatar" src="{{ $cube_profileImg }}" alt="" width="28" height="28" loading="lazy" />
		@else
			<span class="cube-avatar cube-avatar-fallback" aria-hidden="true">{!! $cube_icons['user'] !!}</span>
		@endif
		@if($oDocument->get('member_srl') > 0)
			<a class="cube-read-author member_{{ (int)$oDocument->get('member_srl') }}" href="#popup_menu_area" onclick="return false">{{ $oDocument->getNickName() }}</a>
		@else
			<span class="cube-read-author">{{ $oDocument->getNickName() }}</span>
		@endif
	</div>

	<div class="cube-read-info">
		<div class="cube-read-info-left">
			<time datetime="{{ $oDocument->getRegdateDT() }}">{{ $oDocument->getRegdate('Y.m.d H:i') }}</time>
			<span class="cube-read-info-sep" aria-hidden="true">·</span>
			<span>{!! $cube_icons['eye'] !!}{{ number_format((int)$oDocument->get('readed_count')) }}</span>
			<span class="cube-read-info-sep" aria-hidden="true">·</span>
			<span>{!! $cube_icons['comment'] !!}{{ number_format((int)$oDocument->getCommentCount()) }}</span>
		</div>
		<div class="cube-read-info-right">
			@if($cube['showScrap'] && $is_logged)
				<button type="button" class="cube-icon-btn cube-scrap" data-srl="{{ $oDocument->document_srl }}" aria-label="스크랩" aria-pressed="false">{!! $cube_icons['bookmark'] !!}</button>
			@endif
			<details class="cube-dropdown">
				<summary class="cube-icon-btn cube-dropdown-trigger" aria-label="더보기">{!! $cube_icons['dots'] !!}</summary>
				<ul class="cube-dropdown-menu" role="menu">
					<li role="none"><button type="button" role="menuitem" class="cube-copy-btn" data-url="{{ $oDocument->getPermanentUrl() }}"><span data-role="copy-label">링크복사</span></button></li>
					@if($board_features->document->report ?? false)
						<li role="none"><button type="button" role="menuitem" class="cube-report-btn" data-srl="{{ $oDocument->document_srl }}">신고하기</button></li>
					@endif
					@if($oDocument->isGranted())
						<li role="none"><a role="menuitem" href="{{ getUrl('', 'mid', $mid, 'act', 'dispBoardWrite', 'document_srl', $oDocument->document_srl) }}">수정</a></li>
						<li role="none"><a role="menuitem" href="{{ getUrl('', 'mid', $mid, 'act', 'dispBoardDelete', 'document_srl', $oDocument->document_srl) }}">삭제</a></li>
					@endif
				</ul>
			</details>
		</div>
	</div>
</div>
