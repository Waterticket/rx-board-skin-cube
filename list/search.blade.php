{{-- 검색 폼. board.class.php:15-24 의 화이트리스트를 그대로 select 옵션으로 쓴다. 'nickname'이 아니라 'nick_name'. --}}
@if($cube['showSearch'] && !empty($search_option))
<form class="cube-search" method="get" action="{{ getUrl('') }}">
	<input type="hidden" name="mid" value="{{ $mid }}" />
	@if(!empty($category))
		<input type="hidden" name="category" value="{{ $category }}" />
	@endif
	<select name="search_target" class="cube-search-target" aria-label="검색 대상">
		@foreach($search_option as $cube_opt_key => $cube_opt_label)
			<option value="{{ $cube_opt_key }}" @selected(($search_target ?? '') === $cube_opt_key)>{{ $cube_opt_label }}</option>
		@endforeach
	</select>
	<input type="text" name="search_keyword" class="cube-search-input" placeholder="검색어를 입력하세요" value="{{ $search_keyword ?? '' }}" aria-label="검색어" />
	<button type="submit" class="cube-search-btn" aria-label="검색">{!! $cube_icons['search'] !!}</button>
</form>
@endif
