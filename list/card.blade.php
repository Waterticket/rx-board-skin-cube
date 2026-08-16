{{-- 카드 1건. list/grid.blade.php 에서 항목당 1회만 include 된다 ($document, $isNotice 는 grid가 @php 로 미리 심어 둔다). --}}
@php
	$cube_thumb = null;
	if (!empty($cube_cols['thumbnail']) && $document->isAccessible()) {
		$cube_thumb = $document->getThumbnail((int)$cube['thumbSize'], (int)$cube['thumbSize'], 'fill');
	}

	$cube_badge = null;
	if ($isNotice) {
		$cube_badge = 'notice';
	} elseif ($cube['displayHot'] && (($document->get('voted_count') >= $cube['hotVotes']) || ($document->get('comment_count') >= $cube['hotComments']))) {
		$cube_badge = 'hot';
	} elseif ($cube['displayNew'] && $document->getRegdateTime() > (time() - $cube['durationNew'] * 3600)) {
		$cube_badge = 'new';
	}

	// 라벨은 눌러서 그 분류/게시판으로 갈 수 있게 한다. 게시판 라벨의 mid 는 board.view.php 의
	// _fillModuleTitles(:688) 가 문서마다 채워 준다 — 다른 게시판을 끌어온 목록(include_modules)에서도
	// 각 글의 원래 게시판으로 간다.
	$cube_authorLabel = null;
	$cube_authorLabelUrl = null;
	if ($cube['authorLabel'] === 'category' && $document->get('category_srl') && isset($category_list[$document->get('category_srl')])) {
		$cube_authorLabel = $category_list[$document->get('category_srl')]->title;
		$cube_authorLabelUrl = getUrl('', 'mid', $mid, 'category', $document->get('category_srl'));
	} elseif ($cube['authorLabel'] === 'board') {
		$cube_authorLabel = $document->get('module_title') ?: $module_info->browser_title;
		$cube_authorLabelUrl = getUrl('', 'mid', $document->get('mid') ?: $mid);
	}

	$cube_regTime = $document->getRegdateTime();

	// 글 주소는 목록의 문맥(sort_index·order_type·category·page·검색어)을 물려받지 않는다.
	// getUrl 의 첫 인자를 '' 로 주면 현재 요청 인자를 전부 버리고 새로 만든다(Context.class.php:1784).
	// 어디까지 읽었는지는 sessionStorage 가 기억하므로(js/cube.list.js) 주소에 남길 이유가 없다.
	$cube_docUrl = getUrl('', 'mid', $mid, 'document_srl', $document->document_srl);
@endphp
<article class="cube-card{{ $isNotice ? ' is-notice' : '' }}{{ $cube_thumb ? ' has-thumb' : '' }}">
	{{-- 배지가 없어도 관리자면 이 줄을 그린다 — 체크박스가 카드 오른쪽 위에 놓일 자리가 여기다. 썸네일은 아래 .cube-card-main 기준으로 붙으므로 겹치지 않는다. --}}
	@if($cube_badge || $cube['isManager'])
	<div class="cube-card-top">
		@if($cube_badge === 'notice')
			<span class="cube-badge is-notice">공지</span>
		@elseif($cube_badge === 'hot')
			<span class="cube-badge is-hot">HOT</span>
		@elseif($cube_badge === 'new')
			<span class="cube-badge is-new">NEW</span>
		@endif

		{{-- 코어의 doAddDocumentCart(common/js/common.js:1474)가 값을 모아 document.procDocumentAddCart 로 보낸다. 이 액션은 토글이라 체크/해제 양쪽 다 그대로 호출하면 된다. --}}
		@if($cube['isManager'])
			<label class="cube-card-check">
				<input type="checkbox" name="cart" value="{{ $document->document_srl }}" onclick="doAddDocumentCart(this)"{!! $document->isCarted() ? ' checked="checked"' : '' !!} />
				<span class="sr-only">이 글 선택</span>
			</label>
		@endif
	</div>
	@endif

	<div class="cube-card-main">
		<h3 class="cube-card-title">
			<a href="{{ $cube_docUrl }}" title="{{ $document->getTitleText() }}">{!! $document->getTitle() !!}</a>
			@if(!empty($cube_cols['comment_status']) && $document->isLocked())
				<span class="cube-lock" aria-label="댓글 잠김" title="댓글 잠김">{!! $cube_icons['lock'] !!}</span>
			@endif
		</h3>

		@if(!empty($cube_cols['summary']))
			@php
				$cube_summary = $document->getSummary((int)$cube['summaryLength'], '');
			@endphp
			@if($cube_summary)
				<p class="cube-card-summary"><a href="{{ $cube_docUrl }}">{!! $cube_summary !!}</a></p>
			@endif
		@endif

		@if($cube_thumb)
			<span class="cube-card-thumb">
				<a href="{{ $cube_docUrl }}" tabindex="-1" aria-hidden="true">
					<img src="{{ $cube_thumb }}" alt="" loading="lazy" width="{{ (int)$cube['thumbSize'] }}" height="{{ (int)$cube['thumbSize'] }}" />
					@if((int)$document->get('uploaded_count') > 1)
						<em class="cube-thumb-more">+{{ (int)$document->get('uploaded_count') - 1 }}</em>
					@endif
				</a>
			</span>
		@endif
	</div>

	<div class="cube-card-foot">
		<div class="cube-card-author">
			@if($cube_authorLabel)
				<a class="cube-author-label" href="{{ $cube_authorLabelUrl }}">{{ $cube_authorLabel }}</a>
				<span class="cube-author-sep" aria-hidden="true">·</span>
			@endif
			@if($document->get('member_srl') > 0)
				<a class="cube-author-name member_{{ (int)$document->get('member_srl') }}" href="#popup_menu_area" onclick="return false">{{ $document->getNickName() }}</a>
			@else
				<span class="cube-author-name">{{ $document->getNickName() }}</span>
			@endif
		</div>

		{{-- 댓글수는 comment_count 기본 컬럼이라 목록 설정과 무관하게 항상 쓸 수 있다. 제목 옆이 아니라 여기 하단바 한 곳에만 모은다. --}}
		<div class="cube-card-meta">
			@if(!empty($cube_cols['readed_count']) && (int)$document->get('readed_count') > 0)
				<span class="cube-meta-item cube-meta-view">{!! $cube_icons['eye'] !!}{{ number_format((int)$document->get('readed_count')) }}</span>
			@endif
			@if(!empty($cube_cols['voted_count']) && (int)$document->get('voted_count') > 0)
				<span class="cube-meta-item cube-meta-vote">{!! $cube_icons['heart'] !!}{{ number_format((int)$document->get('voted_count')) }}</span>
			@endif
			@if(!empty($cube_cols['blamed_count']) && (int)$document->get('blamed_count') > 0)
				<span class="cube-meta-item cube-meta-blame">▼{{ number_format((int)$document->get('blamed_count')) }}</span>
			@endif
			@if((int)$document->getCommentCount() > 0)
				<span class="cube-meta-item cube-meta-comment">{!! $cube_icons['comment'] !!}{{ number_format((int)$document->getCommentCount()) }}</span>
			@endif
			@foreach($cube_extra as $cube_ev)
				@php
					$cube_evVal = $document->getExtraValueHTML($cube_ev->idx);
				@endphp
				@if($cube_evVal)
					<span class="cube-meta-item cube-meta-extra">{!! $cube_evVal !!}</span>
				@endif
			@endforeach
		</div>

		<div class="cube-card-foot-right">
			<time class="cube-meta-time" datetime="{{ $document->getRegdateDT() }}" title="{{ getDisplayDateTime($cube_regTime, 'Y-m-d H:i') }}">
				{{ cube_timeago($cube_regTime, $cube) }}
			</time>
			@if($cube['showScrap'] && $is_logged)
				<button type="button" class="cube-scrap" data-srl="{{ $document->document_srl }}" aria-label="스크랩" aria-pressed="false">{!! $cube_icons['bookmark'] !!}</button>
			@endif
		</div>
	</div>
</article>
