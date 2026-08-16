{{-- 카테고리 탭(PC) / select(모바일). 01문서 §4 — 채널 탭을 문서 카테고리로 대체. --}}
@if(($module_info->use_category ?? 'N') === 'Y' && !empty($category_list))
<div class="cube-cats">
	<nav class="cube-cat-tabs" aria-label="카테고리">
		<a class="cube-cat-tab{{ empty($category) ? ' is-active' : '' }}" href="{{ $cube_allUrl }}">전체</a>
		@foreach($category_list as $cube_cat_srl => $cube_cat)
			<a class="cube-cat-tab{{ (string)($category ?? '') === (string)$cube_cat_srl ? ' is-active' : '' }}"
				href="{{ getUrl('category', $cube_cat_srl, 'document_srl', '', 'page', '') }}"
			>{{ str_repeat('　', (int)$cube_cat->depth) }}{{ $cube_cat->title }}</a>
		@endforeach
	</nav>

	<select class="cube-cat-select" aria-label="카테고리" onchange="location.href=this.value">
		<option value="{{ $cube_allUrl }}" @selected(empty($category))>전체</option>
		@foreach($category_list as $cube_cat_srl => $cube_cat)
			<option value="{{ getUrl('category', $cube_cat_srl, 'document_srl', '', 'page', '') }}" @selected((string)($category ?? '') === (string)$cube_cat_srl)>
				{{ str_repeat('　', (int)$cube_cat->depth) }}{{ $cube_cat->title }}
			</option>
		@endforeach
	</select>
</div>
@endif
