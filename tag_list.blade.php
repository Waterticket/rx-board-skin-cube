{{-- board.view.php:830 dispBoardTagList() -> setTemplateFile('tag_list'). --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap">
	<h1 class="cube-form-title">태그</h1>
	@if(!empty($tag_list))
		<div class="cube-tag-cloud">
			@foreach($tag_list as $cube_tag)
				<a class="cube-tag-item" href="{{ getUrl('', 'mid', $mid, 'search_target', 'tag', 'search_keyword', $cube_tag->tag) }}">#{{ $cube_tag->tag }}</a>
			@endforeach
		</div>
	@else
		<p class="cube-empty">등록된 태그가 없습니다.</p>
	@endif
</div>

@include('layout/close')
